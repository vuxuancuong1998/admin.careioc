param([string]$Php = 'C:\xampp\php\php.exe')
$ErrorActionPreference = 'Stop'
$lisRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
Start-Transcript -Path (Join-Path $lisRoot 'scripts\lis-task-install.log') -Force | Out-Null
$lisScript = Join-Path $lisRoot 'scripts\lis-sync.php'
if (!(Test-Path -LiteralPath $Php) -or !(Test-Path -LiteralPath $lisScript)) { throw 'PHP or LIS worker missing.' }
$settings = New-ScheduledTaskSettingsSet -StartWhenAvailable -MultipleInstances IgnoreNew -ExecutionTimeLimit (New-TimeSpan -Minutes 25) -RestartCount 3 -RestartInterval (New-TimeSpan -Minutes 5) -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries
$principal = New-ScheduledTaskPrincipal -UserId 'SYSTEM' -LogonType ServiceAccount -RunLevel Limited
$refreshAction = New-ScheduledTaskAction -Execute $Php -Argument ('"' + $lisScript + '" refresh 10') -WorkingDirectory $lisRoot
$lisNow = Get-Date
$lisNext = $lisNow.Date.AddMinutes(([math]::Floor($lisNow.TimeOfDay.TotalMinutes / 30) + 1) * 30)
$refreshTrigger = New-ScheduledTaskTrigger -Once -At $lisNext -RepetitionInterval (New-TimeSpan -Minutes 30)
$finalAction = New-ScheduledTaskAction -Execute $Php -Argument ('"' + $lisScript + '" final') -WorkingDirectory $lisRoot
$finalTrigger = New-ScheduledTaskTrigger -Daily -At '23:59:59'
Register-ScheduledTask -TaskName 'CAREIOC-LIS-Refresh' -Action $refreshAction -Trigger $refreshTrigger -Settings $settings -Principal $principal -Description 'LIS staging every 30 minutes; recover missing daily snapshots since 2024.' -Force | Out-Null
Register-ScheduledTask -TaskName 'CAREIOC-LIS-Final' -Action $finalAction -Trigger $finalTrigger -Settings $settings -Principal $principal -Description 'Fresh LIS fetch and final daily snapshot at 23:59:59 Vietnam local time.' -Force | Out-Null
Get-ScheduledTask -TaskName 'CAREIOC-LIS-*' | Select-Object TaskName,State
Stop-Transcript | Out-Null
