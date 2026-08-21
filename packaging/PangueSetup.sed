[Version]
Class=IEXPRESS
SEDVersion=3

[Options]
PackagePurpose=InstallApp
ShowInstallProgramWindow=0
HideExtractAnimation=1
UseLongFileName=1
InsideCompressed=1
CAB_FixedSize=0
CAB_ResvCodeSigning=0
ReinstallMode=0
TargetName=C:\Users\Mauricio\Documents\proyecto-registro-asistencias\control-asistencia\dist\Pangue-Setup.exe
FriendlyName=Pangue - Instalador
AppLaunched=PowerShell.exe -NoProfile -ExecutionPolicy Bypass -File install-pangue.ps1
PostInstallCmd=<None>
SourceFiles=SourceFiles

[SourceFiles]
SourceFiles0=C:\Users\Mauricio\Documents\proyecto-registro-asistencias\control-asistencia\packaging

[SourceFiles0]
%FILE0%=C:\Users\Mauricio\Documents\proyecto-registro-asistencias\control-asistencia\packaging\PanguePayload.zip
%FILE1%=C:\Users\Mauricio\Documents\proyecto-registro-asistencias\control-asistencia\packaging\install-pangue.ps1

[Strings]
FILE0=C:\Users\Mauricio\Documents\proyecto-registro-asistencias\control-asistencia\packaging\PanguePayload.zip
FILE1=C:\Users\Mauricio\Documents\proyecto-registro-asistencias\control-asistencia\packaging\install-pangue.ps1
