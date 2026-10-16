<?php
/** Rutas y herramientas del hosting; compatible con PHP 7.1. */
require_once __DIR__.'/env.php';
class EgsHosting
{
    public static function backendRoot() { return dirname(__DIR__); }
    public static function wordpressRoot() { return rtrim(getenv('EGS_WORDPRESS_ROOT') ?: dirname(self::backendRoot()).'/public_html','/'); }
    public static function backupDir() { return rtrim(getenv('EGS_BACKUP_STATE_DIR') ?: dirname(self::backendRoot()).'/.egs-backup','/'); }
    public static function backendUrl() { return rtrim(getenv('EGS_BACKEND_URL') ?: 'https://backend.comercializadoraegs.com','/'); }
    public static function redirectUri() { return getenv('EGS_DRIVE_REDIRECT_URI') ?: self::backendUrl().'/drive-conectar.php'; }
    public static function writesAllowed() { return (getenv('EGS_HOSTING_MODE') ?: 'production') === 'production'; }
    public static function requireWrites()
    {
        if(!self::writesAllowed())throw new RuntimeException('Las escrituras externas están desactivadas en este entorno de migración.');
    }
    public static function binary($name)
    {
        $override=getenv('EGS_BIN_'.strtoupper(str_replace('-','_',$name)));
        if($override){if(!is_file($override)||!is_executable($override))throw new RuntimeException('Herramienta no ejecutable: '.$name);return $override;}
        foreach(explode(PATH_SEPARATOR,getenv('PATH') ?: '/usr/local/bin:/usr/bin:/bin:/usr/sbin') as $dir){$path=rtrim($dir,'/').'/'.$name;if(is_file($path)&&is_executable($path))return $path;}
        if($name==='mysqldump')return self::binary('mariadb-dump');
        if($name==='mysql')return self::binary('mariadb');
        throw new RuntimeException('Falta la herramienta '.$name.'; configura EGS_BIN_'.strtoupper(str_replace('-','_',$name)).'.');
    }
    public static function gpgArguments($home,$key)
    {
        $binary=self::binary('gpg');$lines=array();$status=0;exec(escapeshellarg($binary).' --version 2>/dev/null',$lines,$status);
        if($status!==0||!isset($lines[0])||!preg_match('/(\d+\.\d+(?:\.\d+)?)/',$lines[0],$m))throw new RuntimeException('No se pudo comprobar GnuPG.');
        $args=array($binary,'--no-options','--homedir',$home,'--batch','--yes');
        if(version_compare($m[1],'2.1','>='))$args=array_merge($args,array('--pinentry-mode','loopback'));
        return array_merge($args,array('--passphrase-file',$key));
    }
    public static function dumpOptions()
    {
        $binary=self::binary('mysqldump');$lines=array();$status=0;exec(escapeshellarg($binary).' --version 2>/dev/null',$lines,$status);
        if($status!==0)throw new RuntimeException('No se pudo comprobar mysqldump.');
        return stripos(implode(' ',$lines),'mariadb')!==false?array():array('--set-gtid-purged=OFF');
    }
}
