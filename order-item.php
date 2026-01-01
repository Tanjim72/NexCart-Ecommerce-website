<?php
/**
 * Order Item Component (for invoice/order confirmation)
 * Displays a single item in an order with quantity and subtotal
 * 
 * Usage: <?php include 'components/order-item.php'; ?>
 * 
 * Parameters (pass as $item):
 * - $item['product_name'] (string) - Product name
 * - $item['quantity'] (int) - Quantity ordered
 * - $item['price'] (float) - Price per item
 * - $item['total'] (float) - Total for this item (price * quantity)
 */
?>

<tr class="invoice-row">
    <td class="item-name">
        <?php echo htmlspecialchars($item['product_name']); ?>
    </td>
    <td class="item-qty">
        <?php echo htmlspecialchars($item['quantity']); ?>
    </td>
    <td class="item-price">
        ৳ <?php echo number_format($item['price']); ?>
    </td>
    <td class="item-total">
        ৳ <?php echo number_format($item['quantity'] * $item['price']); ?>
    </td>
</tr>
