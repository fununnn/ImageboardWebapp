<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 60px; }
        .upload-area { 
            border: 2px dashed #ccc; 
            border-radius: 20px; 
            width: 100%; 
            padding: 20px;
            text-align: center;
        }
        .upload-area.highlight { border-color: #007bff; }
        #preview { max-width: 100%; max-height: 300px; margin-top: 20px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">Image Upload Service</a>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">Upload an Image</h1>
        <div class="upload-area" id="uploadArea">
            <p>Drag and drop an image here or click to select</p>
            <input type="file" id="fileInput" style="display: none;" accept="image/*">
        </div>
        <img id="preview" style="display: none;">
        <div id="uploadProgress" class="progress mt-3" style="display: none;">
            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
        </div>
        <button id="uploadBtn" class="btn btn-primary mt-3" style="display: none;">Upload</button>
        <div id="result" class="mt-3"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = uploadProgress.querySelector('.progress-bar');
    const result = document.getElementById('result');

    uploadArea.addEventListener('click', () => fileInput.click());

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        uploadArea.classList.add('highlight');
    }

    function unhighlight() {
        uploadArea.classList.remove('highlight');
    }

    uploadArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        if (files.length > 0) {
            const file = files[0];
            if (file.type.startsWith('image/')) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
                uploadBtn.style.display = 'block';
            } else {
                alert('Please select an image file.');
            }
        }
    }

    uploadBtn.addEventListener('click', uploadFile);

    function uploadFile() {
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);

        uploadProgress.style.display = 'block';
        progressBar.style.width = '0%';
        uploadBtn.disabled = true;

        fetch('/upload', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            uploadProgress.style.display = 'none';
            uploadBtn.disabled = false;
            return response.json();
        })
        .then(data => {
            if (data.success) {
                    result.innerHTML = `
                        <div class="alert alert-success">
                            Image uploaded successfully. <br>
                            URL: <a href="/${data.url}" target="_blank">/${data.url}</a>
                        </div>`;
                } else {
                    result.innerHTML = `<div class="alert alert-danger">Upload failed: ${data.error}</div>`;
                }
        })
        .catch(error => {
            console.error('Error:', error);
            result.innerHTML = '<div class="alert alert-danger">An error occurred during upload.</div>';
            uploadBtn.disabled = false;
        });
    }
    </script>
</body>
</html>