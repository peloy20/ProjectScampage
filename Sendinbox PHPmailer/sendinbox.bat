@REM @Author: Eka Syahwan
@REM @Date:   2017-09-14 06:18:06
@REM @Last Modified by:   ./Peloy20
@REM Modified time: 2018-11-03 08:21:21
@echo off
set PATH=%PATH%;C:\xampp\php
title Sendinbox 2.4
:runsendinbox
php sendinbox.php
pause
cls
goto runsendinbox