<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 60px; }
        .image-container { max-width: 100%; overflow: hidden; }
        .image-container img { max-width: 100%; height: auto; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">Image Upload Service</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">Uploaded Image</h1>
        <div class="image-container mb-3">
            <img src="/<?php echo htmlspecialchars($image['file_path']); ?>" alt="Uploaded Image" class="img-fluid">
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Image Details</h5>
                <p class="card-text">URL: <a href="<?php echo htmlspecialchars($image['url']); ?>" target="_blank"><?php echo htmlspecialchars($image['url']); ?></a></p>
                <p class="card-text">Views: <?php echo htmlspecialchars($image['view_count']); ?></p>
                <p class="card-text">Uploaded: <?php echo htmlspecialchars($image['upload_date']); ?></p>
            </div>
        </div>
        <button id="deleteBtn" class="btn btn-danger">Delete Image</button>
        <a href="/" class="btn btn-primary">Upload Another Image</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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