	</div><!-- /interieurContenu -->
</div><!-- /contenu -->

<?php
if ($menuSousLeContenu)
{
	$menu = construitMenu($racine, $langue);
	echo $menu[0];
	include $menu[1];
	echo $menu[2];
}
?>

<?php if ($basDePage): ?>
	<div id="basDePage">
		<?php include fichierBasDePage($racine, $langue); ?>
	</div><!-- /basDePage -->
<?php endif; ?>

</div><!-- /page -->

</body>
</html>
