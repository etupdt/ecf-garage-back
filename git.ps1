
Write-Host ${Test-Path "C:\Temp\in"} -ForegroundColor Yellow

if (Test-Path "C:\Temp\in") {
    Remove-Item "C:\Temp\in" -Recurse -Force
}

git clone https://github.com/etupdt/ecf-garage-back.git 'C:\Temp\in'

if ($LASTEXITCODE -eq 0) {

    if (Test-Path "C:\Temp\in\.git" -PathType Leaf) {
        Remove-Item "C:\Temp\in\.git" -Recurse -Force
        Remove-Item "C:\Temp\in\.gitignore" -Recurse -Force
        Remove-Item "C:\Temp\in\.vscode" -Recurse -Force
    }
    
    Compress-Archive -Path C:\Temp\in\* -DestinationPath C:\Temp\in\in.zip
    
    if ($LASTEXITCODE -eq 0) {
        scp 'C:\Temp\in\in.zip' 'admin@nasts2311:/share/Web/docker/Applications/ecf-garage-back/'
    } else {
        Write-Host "compress error" -ForegroundColor Red
    }

} else {
    Write-Host "git clone error" -ForegroundColor Red
}
