<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="mb-4">Create New Snippet</h2>
            <form id="snippetForm" class="bg-light p-4 rounded shadow">
                <div class="mb-3">
                    <label for="language" class="form-label">Language</label>
                    <select class="form-select" id="language" name="language">
                        <option value="plaintext">Plain Text</option>
                        <option value="javascript">JavaScript</option>
                        <option value="python">Python</option>
                        <option value="php">PHP</option>
                        <option value="html">HTML</option>
                        <option value="css">CSS</option>
                        <option value="sql">SQL</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="editor" class="form-label">Code</label>
                    <div id="editor" style="height: 400px; border: 1px solid #ddd; border-radius: 4px;"></div>
                </div>
                <div class="mb-3">
                    <label for="expiration" class="form-label">Expiration</label>
                    <select class="form-select" id="expiration" name="expiration">
                        <option value="">Never</option>
                        <option value="<?= date('Y-m-d H:i:s', strtotime('+10 minutes')) ?>">10 minutes</option>
                        <option value="<?= date('Y-m-d H:i:s', strtotime('+1 hour')) ?>">1 hour</option>
                        <option value="<?= date('Y-m-d H:i:s', strtotime('+1 day')) ?>">1 day</option>
                        <option value="<?= date('Y-m-d H:i:s', strtotime('+1 week')) ?>">1 week</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Create Snippet</button>
            </form>
        </div>
    </div>
</div>

<script>
require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.30.1/min/vs' }});
require(["vs/editor/editor.main"], function() {
    var editor = monaco.editor.create(document.getElementById('editor'), {
        value: '',
        language: 'plaintext',
        theme: 'vs-dark',
        minimap: { enabled: false },
        scrollBeyondLastLine: false,
        fontSize: 14,
        lineNumbers: 'on',
        renderLineHighlight: 'all',
        automaticLayout: true
    });

    document.getElementById('language').addEventListener('change', function() {
        monaco.editor.setModelLanguage(editor.getModel(), this.value);
    });

    document.getElementById('snippetForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var content = editor.getValue();
        var language = document.getElementById('language').value;
        var expiration = document.getElementById('expiration').value;

        fetch('/snippet/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ 'content': content, 'language': language, 'expiration': expiration })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.url;
            } else {
                showAlert('danger', 'Failed to create snippet: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'An error occurred while creating the snippet: ' + error.message);
        });
    });

    function showAlert(type, message) {
        var alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.querySelector('.col-md-10').insertBefore(alertDiv, document.querySelector('form'));
    }
});
</script>

<style>
#editor {
    margin-bottom: 20px;
}
.form-label {
    font-weight: bold;
}
</style>