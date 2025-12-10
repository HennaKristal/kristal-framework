<div class="kristal-fatal-block">
    <p><strong>Fatal Error:</strong> <?php echo sanitizeString($message); ?></p>
    <span>Occurred on line <?php echo sanitizeString($line); ?> in file <?php echo sanitizeString($file); ?></span>

    <?php if (!empty($lines)): ?>
        <div class="kristal-code-block">
            <?php for ($i = $start; $i < $end; $i++): ?>
                <?php if ($i + 1 === $line): ?>
                    <div class="kristal-code-highlight-line"><?php echo $i + 1; ?>: <?php echo htmlspecialchars($lines[$i]); ?></div>
                <?php else: ?>
                    <div class="kristal-code-line"><?php echo $i + 1; ?>: <?php echo htmlspecialchars($lines[$i]); ?></div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    body {
        background-color: #f1f1f1 !important;
        font-family: Helvetica, Arial, sans-serif !important;
    }

    .kristal-fatal-block {
        background-color: #ffffff !important;
        color: black !important;
        border: 2px solid #990000 !important;
        width: 60% !important;
        margin: 80px auto !important;
        padding: 32px !important;
    }

    .kristal-code-block {
        background-color: #f9f9f9 !important;
        color: black !important;
        border: 1px solid #cccccc !important;
        padding: 12px !important;
        margin-top: 20px !important;
        overflow-x: auto !important;
        font-family: Courier New, monospace !important;

    }

    .kristal-code-line {
        white-space: pre !important;
    }

    .kristal-code-highlight-line {
        background-color: #ffdddd !important;
        font-weight: bold !important;
        white-space: pre !important;
    }
</style>
