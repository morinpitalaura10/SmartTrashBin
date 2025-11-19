<?php
echo "Admin (admin123): <br>";
echo password_hash('admin123', PASSWORD_DEFAULT);
echo "<br><br>";

echo "OB 1 (ob123): <br>";
echo password_hash('ob123', PASSWORD_DEFAULT);
echo "<br><br>";

echo "OB 2 (ob1234): <br>";
echo password_hash('ob1234', PASSWORD_DEFAULT);
echo "<br><br>";
?>
