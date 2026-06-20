<div class="topbar">
    <h2>Dashboard</h2>
    <div id="right_top_bar">
        <span><?php echo "{$_SESSION['admin_name']}"; ?></span>
        <form action="/web_ban_hang/admin/logout.php" method="post">
            <button type="submit" name="logout">Đăng xuất</button>
        </form>
    </div>
</div>
