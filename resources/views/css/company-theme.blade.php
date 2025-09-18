{{-- Dynamic CSS for {{ $company->name }} --}}
:root {
    /* Company specific colors */
    --primary-color: {{ $colors['primary'] }};
    --primary-hover: {{ $colors['primary_hover'] }};
    --primary-light: {{ $colors['primary_light'] }};
    --primary-border: {{ $colors['primary_border'] }};
    
    @if($company->logo_path)
    --company-logo: url('{{ asset('storage/' . $company->logo_path) }}');
    @endif
    
    /* Rest of the colors remain the same */
    --background-main: #f9fafb;        
    --background-card: #ffffff;        
    --background-secondary: #f3f4f6;   
    --background-hover: rgba(13, 14, 17, 0.5);
    
    --text-primary: #111827;           
    --text-secondary: #6b7280;         
    --text-tertiary: #9ca3af;          
    --text-light: #e5e7eb;             
    --text-white: #ffffff;
    --text-white-dimmed: rgba(255, 255, 255, 0.5);
    --text-white-muted: rgba(255, 255, 255, 0.8);
    
    /* Status colors (keep default for now) */
    --status-success: #10b981;         
    --status-success-light: #d1fae5;   
    --status-success-dark: #047857;    
    --status-success-text: #065f46;    
    --status-success-bg: #f0fdf4;      
    --status-success-border: #bbf7d0;  
    
    --status-info: #3b82f6;            
    --status-info-light: #dbeafe;      
    --status-info-dark: #1d4ed8;       
    --status-info-text: #1e40af;       
    --status-info-bg: #eff6ff;         
    --status-info-border: #93c5fd;     
    
    --status-warning: #f59e0b;         
    --status-warning-light: #fef3c7;   
    --status-warning-dark: #d97706;    
    --status-warning-text: #92400e;    
    --status-warning-bg: #fffbeb;      
    
    --status-danger: #ef4444;          
    --status-danger-light: #fecaca;    
    --status-danger-dark: #dc2626;     
    --status-danger-text: #991b1b;     
    --status-danger-bg: #fef2f2;       
    
    --status-special: #8b5cf6;         
    --status-special-light: #ede9fe;   
    --status-special-dark: #7c3aed;    
    --status-special-text: #5b21b6;    
    
    --status-maintenance: #f97316;     
    --status-maintenance-light: #fed7aa; 
    --status-maintenance-text: #ea580c; 
    
    --border-light: #e5e7eb;           
    --border-medium: #d1d5db;          
    --border-dark: #374151;            
    
    --shadow-light: rgba(0, 0, 0, 0.05);
    --shadow-medium: rgba(0, 0, 0, 0.1);
    --shadow-dark: rgba(0, 0, 0, 0.25);
    
    --border-radius: 4px;
    --border-radius-large: 12px;
    --transition-fast: 150ms;
    --transition-normal: 300ms;
}

/* Company logo styles */
@if($company->logo_path)
.company-logo {
    background-image: var(--company-logo);
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
}
@endif

/* Override button styles with company colors */
.btn-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.btn-primary:hover {
    background-color: var(--primary-hover);
    border-color: var(--primary-hover);
}

/* Company specific utilities */
.bg-company-primary {
    background-color: var(--primary-color);
}

.text-company-primary {
    color: var(--primary-color);
}

.border-company-primary {
    border-color: var(--primary-border);
}
