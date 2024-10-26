<div class="wrap">
<h2>Documentation</h2>
<h3>Introduction</h3>

<p>Le plugin WordPress 123devis est là pour vous aider à facilement intégrer les formulaires 123devis sur votre site et ainsi générer du revenu. </p>

<p>Pour bien débuter, suivez les étapes ci-dessous en commencant par cliquer sur le bouton 123devis dans le menu de gauche (1).</p>
<img style="float:right" src="<?php print plugins_url('settings.png', __FILE__);?>">

<h3>Configurer l’API (2)</h3>
<p>
	La section inférieure de cette page vous permet de configurer l’url de l’API en utilisant les informations fournies par votre contact 123devis.
</p>
<p>L’url devrait être <b>"https://api.servicemagic.eu"</b>.</p>
<p>Pour le serveur, sélectionnez <b>“FR”</b> pour France.</p>

<h3>Vos identifiants (3)</h3>
<p>Entrez les identifiants fournis par votre contact 123devis et cliquez sur <b>“Sauvegarder”</b>. Si vous rencontrez un problème, vérifiez que l’url de l’API est correcte ou bien contactez 123devis.</p>
<?php if (get_option('sm_creds')) { ?>

<br style="clear:both"/>
<hr/>
<h3>2 Méthodes d'intégration des formulaires</h3>
<p>Vous avez deux méthodes pour intégrer vos formulaires à votre site : </p>
<ol>
<li><strong>STANDARD </strong>: Vous permez d'intégrer des formulaires pour une activité / catégorie travaux.com définie;
<ul>
<li><a href='#std1' >Etape 1 : Création d'un formulaire intégrable</a></li>
<li><a href='#std2' >Etape 2 : Intégration d'un formulaire à un article ou une page </a></li>
</ul>
</li>
<li><strong>GENERIQUE </strong>: Vous permez d'intégrer la liste de toutes les activités travaux.com et accédez à tous les formulaires devis.
<ul><li><a href='#gen1' >Etape 1 : Création d'un formulaire intégrable générique,</a></li>
<li><a href='#gen2' >Etape 2 : Génération des pages génériques,</li>
<li<a href='#gen3' >Etape 3 : Intégrer un short code générique à un article ou à une page.</li>
</ul>
</li>
</ol>


<br style="clear:both"/>
<hr/>
<a name='std1' ><h3>Etape STANDARD 1 : Créer un formulaire intégrable </h3></a>

<img style="float:right" src="<?php print plugins_url('to_embeddable_form.png', __FILE__);?>">
<ol>
<li>Maintenant que vos identifiants sont sauvegardés, le plugin 123devis affiche un menu plus étendu (1).</li>
<li>Cliquez sur “Formulaires intégrables” (2),</li>
<li>Puis "Créer nouveau formulaire"(3).</li>
</ol>
<p>Choisissez le type d’affichage que vous désirez (1 ou 2 étapes) puis commencez la configuration du formulaire.</p>
<p>Le <b>“Nom du formulaire”</b> sert à retrouver facilement ce formulaire dans l’interface WordPress. Il est utilisé dans la liste des formulaires intégrables, dans les short-codes au niveau des articles, pages ou de l’éditeur de texte.</p>
<p>Le Code de tracking vous permet de séparer ce formulaire des autres dans les rapports de résultat. </p>
<p>Choisissez ensuite le formulaire 123devis que vous souhaitez utiliser. Selectionnez la catégorie désirée pour faire apparaitre la liste des formulaires disponibles. Une fois un formulaire sélectionné, vous pouvez toujours revenir en arrière en cliquant sur <b>“Modifier”</b>. </p>
<p>Les options d’affichage vous permettent ensuite de personnaliser votre formulaire. Affichez ou non les champs optionnels. Ajouter ou non du texte en haut de page du formulaire.</p>
<hr>

<img style="float:right"  src="<?php print plugins_url('shortcodes.png', __FILE__);?>">
<a name='std2' ><h3>Etape STANDARD 2 : Intégrer un formulaire à un article ou une page en utilisant le short-code</h3></a>
<ol>
<li>Choisir où vous souhaitez faire afficher le formulaire En cliquant sur <b>Articles</b> ou <b>Pages</b> dans le menu principal.</li>
<li>Une fois dans l’éditeur de texte, cliquez sur le générateur de shortcode 123devis (2).</li>
<li>Sélectionnez le formulaire voulu dans la liste des formulaires intégrables (3).</li>
<li>Le shortcode est automatiquement inséré au contenu de l’article/la page.</li>
<li>Vous pouvez maintenant voir votre formulaire 123devis en cliquant sur <b>Aperçu</b>. (5) </li>
</ol>

<br style="clear:both"/>
<hr/>
<img style="float:right"  src="<?php print plugins_url('embeddable_form_categories_list.png', __FILE__);?>">
<a name='gen1' ><h3>Etape GENERIQUE 1 : Créer un formulaire intégrable </h3></a>
<p>La création d'un formulaire intégrable se déroule de la même façon qu'un formulaire intégrable standard (<a href='#std1' >voir ci-dessus étape standard 1)</a>.</p>
<p>Pour déterminer qu'un formulaire est générique, il faut choisir dans la liste des catégories <strong>"Toutes catégories"</strong>.</p>
<br style="clear:both"/>
<hr/>
<img style="float:right"  src="<?php print plugins_url('save_generics_pages.png', __FILE__);?>">
<a name='gen2' ><h3>Etape GENERIQUE 2 : Créer des pages génériques </h3></a>
<ol>
<li>Saisir le titre de la page "Toutes activités", ce titre servira côté administration et sur la page public qui listera toutes les activités 123devis;</li>
<li>Saisir les 2 titres des pages suivantes qui serviront dans la liste des pages côté administration :
<ul><li>a. "liste de catégories",</li><li>b. "formulaire générique";</li></ul>
</li>
<li>Sélectionner le formulaire générique qui sera appliqué;</li>
<li>Sauvegarder les pages en cliquant sur le bouton sauvegarder.</li>
</ol>
<p></p>
<br style="clear:both"/>
<hr/>
<img style="float:right"  src="<?php print plugins_url('short_code_button_list_activities.png', __FILE__);?>">
<a name='gen3' ><h3>Etape GENERIQUE 3 :  Intégrer un short code générique à un article ou à une page</h3></a>
<p>Grâce à la génération des pages génériques, vous allez pouvoir créer un article contenant n'importe quel formulaire de 123devis ou bien une page listant une liste des catégories d'une activité déterminée.</p>
<p>Suite à la génération des pages génériques, sur l'édition d'une page ou d'un article le bouton 123devis de l'éditeur de texte s'enrichit.<br>
Vous avez la possibilité d'intégrer dans un article ou une page les shortcodes suivants :
</p>
<ol>
<li>Liste de toutes les activités,</li>
<li>La liste des catégories pour une activité sélectionnable dans le sous menu de "Liste des activités",</li>
<li>un formulaire précis d'une catégorie sélectionnable dans le sous menu de la catégorie choisie par exemple "Fenêtres (PVC,bois, alu)".</li>

</ol>
<?php } ?>
</div>