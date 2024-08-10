<div class="col-12">
    <form action="#" method="post" id="update-part-form" class="d-flex row">
        <?php if ($part !== null && $part->getId() !== null): ?>
        <input type="hidden" name="id" value="<?= $part->getId() ?>" placeholder="ID"><br>
        <?php endif; ?>
        <input type="text" name="name" value="<?= $part !== null ? htmlspecialchars($part->getName()) : '' ?>" placeholder="Name" required><br>
        <input type="text" name="type" value="<?= $part !== null ? htmlspecialchars($part->getType()) : '' ?>" placeholder="Type" required><br>
        <input type="text" name="brand" value="<?= $part !== null ? htmlspecialchars($part->getBrand()) : '' ?>" placeholder="Brand" required><br>
        <input type="text" name="modelNumber" value="<?= $part !== null ? htmlspecialchars($part->getModelNumber()) : '' ?>" placeholder="Model Number" required><br>
        <input type="text" name="releaseDate" value="<?= $part !== null ? htmlspecialchars($part->getReleaseDate()) : '' ?>" placeholder="Release Date (YYYY-MM-DD)" required><br>
        <textarea name="description" placeholder="Description" required><?= $part !== null ? htmlspecialchars($part->getDescription()) : '' ?></textarea><br>
        <input type="number" name="performanceScore" value="<?= $part !== null ? $part->getPerformanceScore() : '' ?>" placeholder="Performance Score" required><br>
        <input type="number" name="marketPrice" value="<?= $part !== null ? $part->getMarketPrice() : '' ?>" placeholder="Market Price" required><br>
        <input type="number" name="rsm" value="<?= $part !== null ? $part->getRsm() : '' ?>" placeholder="RSM" required><br>
        <input type="number" name="powerConsumptionW" value="<?= $part !== null ? $part->getPowerConsumptionW() : '' ?>" placeholder="Power Consumption (W)" required><br>
        <label>Dimensions (L x W x H):</label><br>
        <input type="number" step="0.01" name="lengthM" value="<?= $part !== null ? $part->getLengthM() : '' ?>" placeholder="Length (meters)" required>
        <input type="number" step="0.01" name="widthM" value="<?= $part !== null ? $part->getWidthM() : '' ?>" placeholder="Width (meters)" required>
        <input type="number" step="0.01" name="heightM" value="<?= $part !== null ? $part->getHeightM() : '' ?>" placeholder="Height (meters)" required><br>
        <input type="number" name="lifespan" value="<?= $part !== null ? $part->getLifespan() : '' ?>" placeholder="Lifespan (years)" required><br>
        <input type="submit" value="Update Part">
    </form>
</div>
<script src="/js/app.js"></script>