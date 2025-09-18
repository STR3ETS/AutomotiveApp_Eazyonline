<?php
// Test file upload independently
echo "Testing file upload...\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "POST request received\n";
    echo "Content-Type: " . $_SERVER['CONTENT_TYPE'] ?? 'Not set' . "\n";
    echo "POST data: " . print_r($_POST, true) . "\n";
    echo "FILES data: " . print_r($_FILES, true) . "\n";
    
    if (isset($_FILES['images'])) {
        echo "Images found!\n";
        foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
            if (is_uploaded_file($tmpName)) {
                echo "Image $index is a valid uploaded file\n";
                echo "Original name: " . $_FILES['images']['name'][$index] . "\n";
                echo "Type: " . $_FILES['images']['type'][$index] . "\n";
                echo "Size: " . $_FILES['images']['size'][$index] . "\n";
            } else {
                echo "Image $index is NOT a valid uploaded file\n";
            }
        }
    } else {
        echo "No images found in \$_FILES\n";
    }
} else {
    // Show form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Test Upload</title>
    </head>
    <body>
        <h1>Test File Upload</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="images[]" multiple accept="image/*">
            <button type="submit">Upload</button>
        </form>
    </body>
    </html>
    <?php
}
?>
