<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload</title>
</head>
<body>
    <h1>Upload an Image</h1>
    <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Upload</button>
    </form>
    <div id="result"></div>

    <script>
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        
        fetch('/upload', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('result').innerHTML = 'Image uploaded successfully. URL: ' + data.url;
            } else {
                document.getElementById('result').innerHTML = 'Upload failed: ' + data.error;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('result').innerHTML = 'An error occurred during upload.';
        });
    });
    </script>
</body>
</html>