<div class="row">
    <div class="col-md-12">
        <h2>Create New Snippet</h2>
        <form id="snippetForm">
            <div class="mb-3">
                <label for="language" class="form-label">Language</label>
                <select class="form-select" id="language" name="language">
                    <option value="plaintext">Plain Text</option>
                    <option value="javascript">JavaScript</option>
                    <option value="python">Python</option>
                    <option value="php">PHP</option>
                    <!-- 他の言語オプションを追加 -->
                </select>
            </div>
            <div class="mb-3">
                <label for="editor" class="form-label">Code</label>
                <div id="editor" style="height: 400px; border: 1px solid #ddd;"></div>
            </div>
            <div class="mb-3">
                <label for="expiration" class="form-label">Expiration</label>
                <select class="form-select" id="expiration" name="expiration">
                    <option value="">Never</option>
                    <option value="<?= date('Y-m-d H:i:s', strtotime('+10 minutes')) ?>">10 minutes</option>
                    <option value="<?= date('Y-m-d H:i:s', strtotime('+1 hour')) ?>">1 hour</option>
                    <option value="<?= date('Y-m-d H:i:s', strtotime('+1 day')) ?>">1 day</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create Snippet</button>
        </form>
    </div>
</div>

<script>
require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.30.1/min/vs' }});
require(["vs/editor/editor.main"], function() {
    var editor = monaco.editor.create(document.getElementById('editor'), {
        value: '',
        language: 'plaintext',
        theme: 'vs-dark'
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
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'content': content,
                'language': language,
                'expiration': expiration
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.url;
            } else {
                alert('Failed to create snippet: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the snippet.');
        });
    });
});
</script>