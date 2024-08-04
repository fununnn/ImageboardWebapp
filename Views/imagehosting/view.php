<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Image</title>
</head>
<body>
    <h1>Uploaded Image</h1>
    <img src="/<?php echo htmlspecialchars($image['file_path']); ?>" alt="Uploaded Image">
    <p>URL: <?php echo htmlspecialchars($image['url']); ?></p>
    <button id="deleteBtn">Delete Image</button>

    <script>
    document.getElementById('deleteBtn').addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this image?')) {
            fetch('/delete/<?php echo $image['url']; ?>', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Image deleted successfully');
                    window.location.href = '/';
                } else {
                    alert('Failed to delete image: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the image.');
            });
        }
    });
    </script>
</body>
</html>