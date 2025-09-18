<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-8">Marketplace System Test</h1>
        
        <!-- Platform Test -->
        <div class="bg-white rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Content Generation Test</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Platform:</label>
                <select id="platformSelect" class="border border-gray-300 rounded px-3 py-2">
                    <option value="marktplaats">Marktplaats</option>
                    <option value="instagram">Instagram</option>
                    <option value="facebook">Facebook</option>
                </select>
                <button onclick="testGenerateContent()" class="ml-3 bg-blue-600 text-white px-4 py-2 rounded">
                    Generate Content
                </button>
            </div>
            
            <div id="contentResult" class="border border-gray-200 rounded p-4 min-h-32 bg-gray-50">
                Click "Generate Content" to test...
            </div>
        </div>
        
        <!-- JavaScript functionality test -->
        <div class="bg-white rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">JavaScript Functionality Test</h2>
            <div class="space-x-3">
                <button onclick="testToast()" class="bg-green-600 text-white px-4 py-2 rounded">Test Toast</button>
                <button onclick="testConsoleLog()" class="bg-blue-600 text-white px-4 py-2 rounded">Test Console</button>
            </div>
            <div id="jsTestResult" class="mt-4 p-3 bg-gray-100 rounded">
                JavaScript test results will appear here...
            </div>
        </div>
    </div>

    <script>
        function testGenerateContent() {
            const platform = document.getElementById('platformSelect').value;
            const resultDiv = document.getElementById('contentResult');
            
            resultDiv.innerHTML = '<div class="text-center"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</div>';
            
            fetch('/test-marketplace/preview', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    platform: platform
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = `
                        <div class="space-y-3">
                            <div>
                                <strong>Platform:</strong> ${data.platform}
                            </div>
                            <div>
                                <strong>Title:</strong><br>
                                <div class="bg-white p-2 rounded border">${data.title}</div>
                            </div>
                            <div>
                                <strong>Description:</strong><br>
                                <div class="bg-white p-2 rounded border whitespace-pre-line">${data.description}</div>
                            </div>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = '<div class="text-red-600">Error: Failed to generate content</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<div class="text-red-600">Error: ' + error.message + '</div>';
            });
        }
        
        function testToast() {
            showToast('Test toast notification!', 'success');
        }
        
        function testConsoleLog() {
            console.log('Test console log from marketplace system');
            document.getElementById('jsTestResult').innerHTML = 'Console log test completed - check browser console';
        }
        
        function showToast(message, type = 'info') {
            const colors = {
                success: 'bg-green-600',
                error: 'bg-red-600',
                warning: 'bg-yellow-600',
                info: 'bg-blue-600'
            };
            
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300`;
            toast.textContent = message;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }
        
        // Test initialization
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Test page loaded successfully');
            document.getElementById('jsTestResult').innerHTML = 'Page loaded - JavaScript is working';
        });
    </script>
</body>
</html>
