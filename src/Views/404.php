<?php

$title = "404 Not Found";
$content = '
<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:60vh;">
    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" style="margin-bottom:20px;">
        <circle cx="12" cy="12" r="10" stroke="#e74c3c" stroke-width="2" fill="#f9ebea"/>
        <text x="12" y="16" text-anchor="middle" font-size="32" fill="#e74c3c" font-family="Arial" font-weight="bold">404</text>
    </svg>
    <h2 style="color:#e74c3c; margin-bottom:10px;">Page Not Found</h2>
    <p style="color:#555; margin-bottom:20px;">Sorry, the page you are looking for does not exist.</p>
    <a href="/store.php" style="padding:10px 20px; background:#3498db; color:#fff; border-radius:4px; text-decoration:none;">Go to Stores</a>
</div>
';
include __DIR__ . '/layout.php';