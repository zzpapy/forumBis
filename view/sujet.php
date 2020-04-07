<div class="list_sujet">
    <?php 
    // var_dump($_SESSION);die; 
    if(isset($_SESSION["user"])){ 
        echo "<div class='cache_crea_sujet'>
        <p>Vous souhaitez créer un sujet cliquer ici !!!</p>
        <input class='input' type='text' name='titre'>
        <p>Ou particper à une conversation existante ci-dessous</p></div>";
        echo '<div class="crea_sujet hide">
        <p>Une question, un problème ou juste envie de discuter créez un sujet ici</p>
        <form action="index.php?action=crea_sujet" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="membre_id" value='.$_SESSION["user"]->getId().'>
        <input class="input" type="text" name="titre">
        <div>
        <label for="photo" class="label-file"><span class="label_photo"> choisir une image(option)</span> <i class="far fa-2x fa-image"></i></label>
        <img id="blah" src="public/images/no.jpg" alt="your image" />
        <input type="file"class="input-file" name="photo" id="photo" onchange="readURL(this);">
        <input type="submit"  class="button_form" name="crea_sujet">
        </div>
        </form>
        </div>';
    } 
   
    if(!is_object($_SESSION["liste"]) && $_SESSION["liste"] != ''){
        $nb_sujet = count($_SESSION["liste"]);
    }
    else{
        $nb_sujet = 0;
    }
    
    
    ?>
    <p>Sujet en cours actuellement : <?php echo $nb_sujet ?></p>
    <ul class="list">
        <?php 
            if(isset($_SESSION["user"])){
                $user = $_SESSION["user"];
                $membre_id = $user->getId();
            }
            else{
                $membre_id =0;
            }
            if(isset($_SESSION["liste"]) && !is_object($_SESSION["liste"]) ){
                $i = 0;
                // var_dump($_SESSION["liste"]);die;
                foreach ($_SESSION["liste"] as $key => $value) {
                    $date = new \DateTime($value->getDate());
                    $sujet_id = $value->getId();
                    $close = $value->getClose();
                    $date = $date->format('d/m/Y H:i');
                    $by = $value->getMembre()->getPseudo();
                    $titre = $value->getTitre();
                    $author = $value->getMembre()->getId();
                    $photo = $value->getPhoto();
                    if(isset($_SESSION["views"][$sujet_id])){
                        $views = $_SESSION["views"][$sujet_id];
                    }
                    else{
                        $views = 0;
                    }
                    if(array_key_exists($value->getId(),$_SESSION["mess"])){
                        $nb_post = $_SESSION["mess"][$value->getId()];
                    }
                    else{
                        $nb_post = 0;
                    }
                    // var_dump($nb_post);die;
                    include('liste_sujet.php');
                }
            }
            else if(is_object($_SESSION["liste"])){
                $nb_sujet = 1;
                $close = $_SESSION["liste"]->getClose();
                $sujet_id = $_SESSION["liste"]->getId();
                $date = new \DateTime($_SESSION["liste"]->getDate());
                $sujet_id = $_SESSION["liste"]->getId();
                // $membre_id = $user->getId();
                $date = $date->format('d/m/Y H:i');
                $by = $_SESSION["liste"]->getMembre()->getPseudo();
                $titre = $_SESSION["liste"]->getTitre();
                $message_id = $_SESSION["liste"]->getId();
                $author = $_SESSION["liste"]->getMembre()->getId();
                $photo = $_SESSION["liste"]->getPhoto();
                if(array_key_exists($_SESSION["liste"]->getId(),$_SESSION["mess"])){
                    $nb_post = $_SESSION["mess"][$_SESSION["liste"]->getId()];
                }
                else{
                    $nb_post = 0;
                }
                if(isset($_SESSION["views"][$sujet_id])){
                    $views = $_SESSION["views"][$sujet_id];
                }
                else{
                    $views = 0;
                }
                include('liste_sujet.php');
                // var_dump(is_object($_SESSION["liste"]));die;
            }
            else{
                echo "Soyez le premier à créer un sujet de conversation";
            }
        ?>
    </ul>
</div>