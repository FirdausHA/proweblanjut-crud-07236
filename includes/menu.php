<?php
// Data menu
$menu_items = array(
    'dashboard' => array(
        'icon' => 'fas fa-th-large',
        'title' => 'Dashboard',
        'link' => 'index.php',
        'active' => (!isset($_GET['page']) || empty($_GET['page']) || $_GET['page'] == 'dashboard') && 
                    (basename($_SERVER['PHP_SELF']) == 'index.php')
    ),
    'data_barang' => array(
        'icon' => 'fas fa-box',
        'title' => 'Data Barang',
        'link' => 'index.php?page=data_barang',
        'active' => (isset($_GET['page']) && $_GET['page'] == 'data_barang') || 
                    (basename($_SERVER['PHP_SELF']) == 'tambah.php') ||
                    (basename($_SERVER['PHP_SELF']) == 'edit.php') ||
                    (basename($_SERVER['PHP_SELF']) == 'detail.php')
    )
);
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-boxes"></i>
            <span>SIGUDHANG</span>
        </div>
    </div>
    
    <nav class="main-menu">
        <ul>
            <?php foreach($menu_items as $id => $item): ?>
            <li>
                <a href="<?php echo $item['link']; ?>" class="<?php echo $item['active'] ? 'active' : ''; ?>">
                    <i class="<?php echo $item['icon']; ?>"></i>
                    <span><?php echo $item['title']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
