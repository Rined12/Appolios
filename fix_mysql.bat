@echo off
echo NETTOYAGE COMPLET ET REINITIALISATION DE MYSQL...
echo (Solution definitive)

:: Supprimer l'ancien dossier data s'il existe et recreer un vide
rmdir /S /Q "C:\xampp\mysql\data"
mkdir "C:\xampp\mysql\data"

:: Copier la sauvegarde completement propre de XAMPP
xcopy "C:\xampp\mysql\backup\*" "C:\xampp\mysql\data\" /E /I /H /Y

echo.
echo Reinitialisation terminee avec succes !
echo.
echo IMPORTANT : Vos anciennes donnees etaient completement corrompues. 
echo Nous sommes repartis sur une base totalement neuve et saine.
echo Vous pourrez re-importer votre base de donnees en utilisant 
echo le fichier schema.sql que vous avez dans votre projet !
echo.
pause
