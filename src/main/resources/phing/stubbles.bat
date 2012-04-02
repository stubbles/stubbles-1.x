@echo off
echo Stubbles setup v${version}
echo (c) 2007-2011 Stubbles Development Team
echo.

if %1!==! goto setup
if %1==setup goto setup
if %1==clean-dist goto execute
if %1==clear-cache goto execute
goto unknown

:setup
phing -f build-stubbles.xml setup
goto end

:execute
phing -f build-stubbles.xml %1
goto end

:unknown
echo Unknown command

:end