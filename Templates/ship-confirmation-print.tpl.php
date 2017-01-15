<form method="post">
    <?= \Lightning\Tools\Form::renderTokenInput(); ?>
    <input type="hidden" name="id" value="<?= $order->id; ?>">
    <h2>Would you like you ship this order:</h2>
    <p>Order ID: <?= $order->id; ?></p>
    <p>Ship to:<br>
        <?= $order->getShippingAddress()->getHTMLFormatted(); ?></p>
    <p>Email: <?= $order->getUser()->email; ?></p>
    <p><strong>Package:</strong>
        Dimensions: <input type="text" name="package-length"><br>
        <input type="text" name="package-height"><br>
        <input type="text" name="package-width"><br>
        Weight: <input type="text" name="package-weight"><br>
        <select name="package-weight-units">
            <option value="oz">Ounces</option>
            <option value="lb">Pounds</option>
        </select>
    </p>
    <p><label><input type="checkbox" name="print-label" value="true"> Print Label</label></p>
    <a href="/admin/orders?action=edit&id=<?= $order->id; ?>" class="button">Cancel</a> <input type="submit" name="submit" value="Ship" class="button red" />
</form>
