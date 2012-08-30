<?php
if ($premierOuDernier == 'premier')
{
	// On vérifie si le cache partiel existe ou s'il est expiré.
	if (file_exists($cheminFichierCachePartiel) && !cacheExpire($cheminFichierCachePartiel, $dureeCachePartiel) && !$estPageCron && !$desactiverLectureCachePartiel)
	{
		@readfile($cheminFichierCachePartiel);
		$inclureFinMilieuInterieurContenu = FALSE;
		include $racine . '/inc/dernier.inc.php';
		
		exit(0);
	}
	else
	{
		ob_start();
	}
}
elseif ($premierOuDernier == 'dernier' && $inclureFinMilieuInterieurContenu)
{
	$codePartielPage = ob_get_contents();
	ob_end_clean();
	
	if ($tableDesMatieres)
	{
		$codePartielPage = tableDesMatieres($codePartielPage, 'div#milieuInterieurContenu', $tDmBaliseTable, $tDmBaliseTitre, $tDmNiveauDepart, $tDmNiveauArret);
	}
	
	creeDossierCache($racine);
	$enregistrerCachePartiel = TRUE;
	
	if (file_exists($cheminFichierCachePartiel))
	{
		$codePartielPageCache = @file_get_contents($cheminFichierCachePartiel);
		
		if ($codePartielPageCache !== FALSE && md5($codePartielPageCache) == md5($codePartielPage))
		{
			$enregistrerCachePartiel = FALSE;
		}
	}
	
	if ($enregistrerCachePartiel)
	{
		@file_put_contents($cheminFichierCachePartiel, $codePartielPage);
	}
	else
	{
		@touch($cheminFichierCachePartiel);
	}
	
	echo $codePartielPage;
}
?>
