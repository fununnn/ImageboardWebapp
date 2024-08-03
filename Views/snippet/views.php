<div class="row">
    <div class="col-md-12">
        <h2>Snippet View</h2>
        <div id="editor" style="height: 400px; border: 1px solid #ddd;"></div>
        <p>Created at: <?= htmlspecialchars($snippet['created_at']) ?></p>
        <?php if ($snippet['expiration']): ?>
            <p>Expires at: <?= htmlspecialchars($snippet['expiration']) ?></p>
        <?php endif; ?>
    </div>
</div>

<script>
require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.30.1/min/vs' }});
require(["vs/editor/editor.main"], function() {
    var editor = monaco.editor.create(document.getElementById('editor'), {
        value: <?= json_encode($snippet['content']) ?>,
        language: <?= json_encode($snippet['language']) ?>,
        theme: 'vs-dark',
        readOnly: true
    });
});
</script>