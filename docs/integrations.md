# External API Integrations

This document explains how to set up and use the external API integrations in the Automotive App.

## Overview

The application supports two main external API integrations:

1. **Marketplace API** - For publishing car listings (OAuth2 with refresh tokens)
2. **Google Analytics 4** - For fetching active users metrics (Service Account authentication)

## Quick Setup

### Prerequisites

- Laravel 10+/11 with queue workers enabled
- Database migrations applied
- Environment variables configured

### 1. Configure Environment Variables

Add the following to your `.env` file:

```bash
# Marketplace Integration
MARKETPLACE_BASE_URL=https://api.marketplace.example.com
MARKETPLACE_CLIENT_ID=your_client_id
MARKETPLACE_CLIENT_SECRET=your_client_secret
MARKETPLACE_REDIRECT_URI=${APP_URL}/oauth/marketplace/callback
MARKETPLACE_SCOPE="read write"
MARKETPLACE_SANDBOX=true
MARKETPLACE_MOCK=true

# Google Analytics 4
GA4_PROPERTY_ID=your_property_id
GA4_CREDENTIALS=storage/app/ga4.json
GA4_MOCK=true
```

### 2. Run Migrations

```bash
php artisan migrate
```

This will create the `api_integrations` and `analytics_metrics` tables.

### 3. Enable Mock Mode (Development)

For development, both integrations default to mock mode:

- `MARKETPLACE_MOCK=true` - Returns fake marketplace responses
- `GA4_MOCK=true` - Returns realistic fake analytics data

## Marketplace Integration

### OAuth Setup

1. **Get OAuth Credentials**: Register your application with the marketplace to get `client_id` and `client_secret`

2. **Initiate OAuth Flow**: Visit `/oauth/marketplace/redirect` while logged in to a tenant

3. **Handle Callback**: The system automatically processes the callback and stores tokens

### Publishing Listings

#### Programmatically

```php
use App\Jobs\PublishListingJob;

// Dispatch job to publish a car listing
PublishListingJob::dispatch($tenantId, $carId);
```

#### Via API

```bash
# Publish a listing
POST /tenants/{tenant_id}/listings/{car_id}/publish

# Get listing status
GET /tenants/{tenant_id}/listings/{car_id}/status
```

### Example: Publishing a Car

```php
use App\Services\Marketplace\MarketplaceClient;
use App\Repositories\ApiIntegrationRepository;
use App\DTO\ListingDTO;

$repository = new ApiIntegrationRepository();
$client = new MarketplaceClient($repository, config('services.marketplace.base_url'), $tenantId);

$listing = new ListingDTO(
    title: 'BMW 3 Serie (2020)',
    description: 'Excellent condition, low mileage...',
    priceCents: 2850000, // €28,500
    currency: 'EUR',
    condition: 'used',
    images: ['https://example.com/image1.jpg']
);

$response = $client->createListing($listing);

if ($response->success) {
    $externalId = $response->getData('id');
    
    // Upload images
    foreach ($car->images as $image) {
        $client->uploadImage($externalId, $image->path);
    }
    
    // Publish
    $client->publish($externalId);
}
```

## Google Analytics 4 Integration

### Service Account Setup

1. **Create Service Account**: In Google Cloud Console, create a service account for GA4 access

2. **Download Credentials**: Download the JSON credentials file

3. **Store Credentials**: Place the file at `storage/app/ga4.json`

4. **Grant Access**: Add the service account email to your GA4 property with Viewer permissions

### Fetching Active Users

#### Via API

```bash
# Get current active users
GET /tenants/{tenant_id}/analytics/active-users

# Response
{
  "success": true,
  "active_users": 15,
  "is_cached": false,
  "last_updated": "2025-01-09T14:30:00Z"
}
```

#### Programmatically

```php
use App\Services\Analytics\Ga4Client;

$ga4Client = new Ga4Client($tenantId);

// Get live data
$activeUsers = $ga4Client->getActiveUsersLast30Min();

// Get cached data with metadata
$result = $ga4Client->getActiveUsersWithCache();
echo "Active users: " . $result['active_users'];
echo "Is mock: " . ($result['is_mock'] ? 'Yes' : 'No');
```

#### Scheduled Sync

```php
use App\Jobs\SyncGa4ActiveUsersJob;

// Schedule regular syncing (add to scheduler)
SyncGa4ActiveUsersJob::dispatch($tenantId);
```

## Mock Mode

### Development Benefits

Both integrations support mock mode for development:

- **No external API calls** - Works offline
- **Realistic data** - Returns believable fake data
- **Consistent behavior** - Predictable responses for testing
- **Rate limit simulation** - Helps test error handling

### Mock Responses

#### Marketplace Mock Data

```json
{
  "success": true,
  "data": {
    "id": "mock_listing_abc123",
    "title": "BMW 3 Serie (2020)",
    "status": "published",
    "listing_url": "https://marketplace.example.com/listings/mock_listing_abc123"
  }
}
```

#### GA4 Mock Data

- Returns 0-20 active users based on time of day
- Higher during business hours (9-17)
- Lower at night (0-6)
- Includes `X-Mock: true` header

### Switching to Production

1. **Set up real credentials**:
   ```bash
   MARKETPLACE_MOCK=false
   GA4_MOCK=false
   ```

2. **Configure real endpoints**:
   ```bash
   MARKETPLACE_BASE_URL=https://api.realmarketplace.com
   GA4_PROPERTY_ID=123456789
   ```

3. **Test OAuth flow** with real marketplace

4. **Upload GA4 credentials** to `storage/app/ga4.json`

## Error Handling

### Marketplace Errors

- **401 Unauthorized**: Automatically refreshes tokens once
- **429 Rate Limited**: Respects `Retry-After` header with exponential backoff
- **422 Validation**: Returns detailed validation errors

### GA4 Errors

- **Invalid credentials**: Falls back to mock data
- **API errors**: Logs error and returns cached data if available

### Job Failures

- **Automatic retries**: 5 attempts with exponential backoff
- **Dead letter queue**: Failed jobs are marked in database
- **Error tracking**: All failures logged with correlation IDs

## Queue Configuration

### Job Priorities

```php
// High priority: Real-time requests
PublishListingJob::dispatch($tenantId, $carId)->onQueue('high');

// Normal priority: Background sync
SyncGa4ActiveUsersJob::dispatch($tenantId)->onQueue('default');
```

### Recommended Queue Setup

```bash
# High priority worker
php artisan queue:work --queue=high --timeout=300

# Default worker
php artisan queue:work --queue=default --timeout=60
```

## API Endpoints

### Authentication Required

All API endpoints require authentication and tenant context.

### Marketplace Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/oauth/marketplace/redirect` | Start OAuth flow |
| GET | `/oauth/marketplace/callback` | OAuth callback |
| POST | `/tenants/{tenant}/listings/{id}/publish` | Publish listing |
| GET | `/tenants/{tenant}/listings/{id}/status` | Get listing status |

### Analytics Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/tenants/{tenant}/analytics/active-users` | Get active users |

### Example Requests

```bash
# Publish a car listing
curl -X POST "https://yourapp.com/tenants/1/listings/123/publish" \
  -H "Authorization: Bearer your_token" \
  -H "Content-Type: application/json"

# Get active users
curl "https://yourapp.com/tenants/1/analytics/active-users" \
  -H "Authorization: Bearer your_token"
```

## Testing

### Running Tests

```bash
# Run all integration tests
php artisan test --testsuite=Feature --filter=Integration

# Run specific test classes
php artisan test tests/Feature/MarketplaceClientTest.php
php artisan test tests/Feature/Ga4ClientTest.php
php artisan test tests/Feature/MarketplaceOAuthTest.php
```

### Test Coverage

The test suite covers:

- OAuth flow end-to-end
- Token refresh on 401 errors
- Rate limiting with backoff
- Mock mode functionality
- Multi-tenant data isolation
- Error handling scenarios

## Monitoring & Debugging

### Logs

All API operations are logged with structured context:

```bash
# View marketplace logs
tail -f storage/logs/laravel.log | grep "Marketplace API"

# View GA4 logs
tail -f storage/logs/laravel.log | grep "GA4 API"
```

### Database Monitoring

```sql
-- Check integration status
SELECT tenant_id, provider, expires_at, created_at 
FROM api_integrations;

-- Check recent metrics
SELECT tenant_id, metric_name, metric_value, recorded_at 
FROM analytics_metrics 
WHERE recorded_at > NOW() - INTERVAL 1 HOUR;

-- Check failed jobs
SELECT * FROM failed_jobs WHERE payload LIKE '%PublishListingJob%';
```

### Health Checks

```php
// Check if marketplace integration is healthy
$healthy = app(ApiIntegrationRepository::class)
    ->hasValidIntegration($tenantId, 'marketplace');

// Check latest GA4 data
$ga4Client = new Ga4Client($tenantId);
$latest = $ga4Client->getLatestMetric('active_users_30min');
```

## Troubleshooting

### Common Issues

1. **OAuth not working**: Check redirect URI matches exactly
2. **Token expired**: Verify refresh token logic is working
3. **Mock mode stuck**: Check environment variables
4. **No active users**: Verify GA4 property ID and credentials
5. **Jobs not processing**: Ensure queue workers are running

### Debug Mode

Enable detailed logging:

```bash
LOG_LEVEL=debug
```

This will log all HTTP requests, responses, and internal operations.

## Production Considerations

### Security

- Store credentials securely (use Laravel secrets for production)
- Rotate refresh tokens regularly
- Monitor for suspicious API usage
- Implement rate limiting on your endpoints

### Performance

- Use Redis for queue backend in production
- Monitor queue length and processing times
- Cache GA4 responses (5-minute default)
- Use database indexes on `tenant_id` and timestamps

### Scalability

- Consider dedicated queue workers for API jobs
- Implement circuit breakers for external API failures
- Use horizontal scaling for queue processing
- Monitor external API quotas and limits

### Monitoring

- Set up alerts for job failures
- Monitor external API response times
- Track integration success rates
- Monitor token expiration and refresh rates
