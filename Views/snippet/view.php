<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Snippet View</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Snippet Information</h5>
                    <p class="card-text"><strong>Language:</strong> <?= htmlspecialchars($snippet['language']) ?></p>
                    <p class="card-text"><strong>Created at:</strong> <?= htmlspecialchars($snippet['created_at']) ?></p>
                    <?php if ($snippet['expiration']): ?>
                        <p class="card-text"><strong>Expires at:</strong> <?= htmlspecialchars($snippet['expiration']) ?></p>
                    <?php else: ?>
                        <p class="card-text"><strong>Expiration:</strong> Never</p>
                    <?php endif; ?>
                    <p class="card-text"><strong>Unique URL:</strong> 
                        <a href="<?= htmlspecialchars('/snippet/view/' . $snippet['unique_url']) ?>">
                            <?= htmlspecialchars('/snippet/view/' . $snippet['unique_url']) ?>
                        </a>
                    </p>
                </div>
            </div>
            <div id="editor" style="height: 400px; border: 1px solid #ddd;"></div>
        </div>
    </div>
</div>

<script>
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.30.1/min/vs' }});
    require(['vs/editor/editor.main'], function() {
        var editor = monaco.editor.create(document.getElementById('editor'), {
            value: <?= json_encode($snippet['content']) ?>,
            language: <?= json_encode($snippet['language']) ?>,
            theme: 'vs-dark',
            readOnly: true,
            minimap: { enabled: false },
            scrollBeyondLastLine: false,
            fontSize: 14,
            lineNumbers: 'on',
            renderLineHighlight: 'all',
            automaticLayout: true
        });

        window.addEventListener('resize', function() {
            editor.layout();
        });
    });
</script>

<style>
    #editor {
        margin-top: 20px;
        margin-bottom: 20px;
        border-radius: 4px;
        overflow: hidden;
    }
</style>
