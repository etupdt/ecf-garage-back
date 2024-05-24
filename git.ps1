
$content = 'GIT_REPO=https://github.com/etupdt/ecf-garage-back.git --branch feature/monolog' + "`n`r"
$content += 'BUILD_OPTIONS=--no-cache' + "`n`r"
# $content += 'ENV=development'
$content += 'ENV=production'

Set-Content C:\Temp\deploy $content

scp 'nas-deploy.yml' 'admin@nasts2311:/share/Web/docker/Applications/sas/'
