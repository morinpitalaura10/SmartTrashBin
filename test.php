<?php
echo "Admin (admin123): <br>";
echo password_hash('admin123', PASSWORD_DEFAULT);
echo "<br><br>";

echo "OB 1 (ob123): <br>";
echo password_hash('ob123', PASSWORD_DEFAULT);
echo "<br><br>";

echo "OB 2 (ob234): <br>";
echo password_hash('ob234', PASSWORD_DEFAULT);
echo "<br><br>";
?>
