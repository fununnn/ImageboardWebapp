<?php include 'layout/header.php'; ?>

<div class="container">
    <h1 class="mb-4">Welcome to Our Service</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Image Sharing</h5>
                    <p class="card-text">Upload and share your images easily.</p>
                    <a href="/upload" class="btn btn-primary">Upload Image</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Code Snippet Sharing</h5>
                    <p class="card-text">Share your code snippets with others.</p>
                    <a href="/snippet/create" class="btn btn-primary">Create Snippet</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>