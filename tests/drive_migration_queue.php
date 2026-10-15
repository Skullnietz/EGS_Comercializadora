<?php
require __DIR__.'/drive_storage_migration.php';copy($sourceRoot.'/config/DriveMigration.php',$testApp.'/config/DriveMigration.php');require $testApp.'/config/DriveMigration.php';
$db=EgsDriveMigration::database();$insert=$db->prepare('INSERT INTO jobs(path,site,context,bytes,updated_at)VALUES(?,?,?,?,?)');
foreach(array(array('backend/vistas/img/a.jpg','backend',10),array('wordpress/wp-content/uploads/b.jpg','wordpress',20),array('backend/vistas/img/c.jpg','backend',30)) as $p)$insert->execute(array($p[0],$p[1],'{}',$p[2],time()));
$first=EgsDriveMigration::claim(false,'backend');$second=EgsDriveMigration::claim(false,'backend');$third=EgsDriveMigration::claim(false,'wordpress');
check($first['id']!==$second['id']&&$third['site']==='wordpress','Dos procesos reclamaron el mismo trabajo');check(EgsDriveMigration::claim(false)===null,'Se reclamó un trabajo con un arrendamiento vigente');
EgsDriveMigration::finish($first,array('status'=>'verified','bytes'=>10));check(EgsDriveMigration::claim(false)===null,'Se repitió una copia completa ya verificada');
$release=EgsDriveMigration::claim(true);check($release['id']===$first['id'],'No se seleccionó una copia verificada para liberar');EgsDriveMigration::finish($release,array('status'=>'released','bytes'=>10));
$db->exec('UPDATE jobs SET lease_until=0 WHERE id='.(int)$second['id']);$recovered=EgsDriveMigration::claim(false);check($recovered['id']===$second['id'],'No se recuperó el trabajo interrumpido');
check(EgsDriveMigration::fail($recovered,new DomainException('Conflicto de versión'))==='review','Un conflicto se volvió a intentar automáticamente');
check(EgsDriveMigration::fail($third,new RuntimeException('Interrupción de Google'))==='retry','Una interrupción no quedó pendiente para reintentar');check(EgsDriveMigration::claim(false)===null,'Se ignoró el tiempo de espera entre reintentos');
$progress=EgsDriveMigration::progress();check($progress['released']===1&&$progress['released_bytes']===10&&$progress['needs_review']===1&&$progress['pending']===1,'El progreso no coincide con los resultados');
$db->exec("UPDATE jobs SET status='already_remote' WHERE id=".(int)$second['id']);$db->exec("UPDATE jobs SET status='released' WHERE id=".(int)$third['id']);check(EgsDriveMigration::progress()['phase']==='complete','La cola no reconoció los originales que WordPress ya había retirado');
echo "PASS: cola sin duplicados, recuperación, copia/retiro por fases, conflictos, reintentos y progreso.\n";
