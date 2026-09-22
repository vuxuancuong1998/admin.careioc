$ErrorActionPreference='Stop'
$lisResults=@()
foreach($lisName in @('CAREIOC-LIS-Refresh','CAREIOC-LIS-Final')) {
 $lisTask=Get-ScheduledTask -TaskName $lisName
 $lisInfo=Get-ScheduledTaskInfo -TaskName $lisName
 $lisResults+=@{name=$lisName;state=[string]$lisTask.State;nextRun=$lisInfo.NextRunTime.ToString('s');result=$lisInfo.LastTaskResult;xml=(Export-ScheduledTask -TaskName $lisName)}
}
$lisResults | ConvertTo-Json -Depth 6 | Set-Content -LiteralPath (Join-Path $PSScriptRoot 'lis-task-status.json') -Encoding UTF8
Start-ScheduledTask -TaskName 'CAREIOC-LIS-Refresh'
