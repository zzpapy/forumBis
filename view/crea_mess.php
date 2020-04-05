<?php 
// var_dump($_SESSION);die;
if(isset($_GET["sujet_id"])){
    $sujet_id = $_GET["sujet_id"];
    $id = $_SESSION["user"]->getId();
    if(isset($result["data"]["subMess"])){
        $subMess = $result["data"]["subMess"];
        
    }
    else{
        $subMess = false;
    }
    // var_dump($_SESSION);die;
    
}
else{
    $sujet_id = '';
}
?>
<div class="list_sujet">
    <div class="crea_mess">
        <h3> Titre du sujet : <?php echo $result["data"]["sujet"] ?></h3>
        <p>Nouveau message</p>
        <form action="index.php?action=crea_mess&sujet_id=<?php echo $sujet_id ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="membre_id" value="<?= $id ?>">
            <input class="input" type="text" name="content">
            <div>
                <label for="photo" class="label-file"><i class="far fa-2x fa-image"></i></label>
                <input  type="file" class="input-file" name="photo" id="photo">
                <input type="hidden" name="sujet_id" value="<?= $sujet_id ?>">
                <input type="submit" class=" button_form" name="crea_mess">
            </div>
        </form>
    </div>
    <div class="block_mess">
        <p>liste des messages</p>
        <?php 
        $msg = "";
            if($result["data"]["mess"] !=NULL){
                if(!is_object($result["data"]["mess"])){
                    foreach ($result["data"]["mess"] as $key => $value) {
                        $user = $_SESSION["user"];
                        $id_mess = $value->getId();
                        $author = $value->getMembre()->getId();
                        $pseudo = $value->getMembre()->getPseudo();
                        $date = $value->getDate();
                        $date = new \DateTime($date);
                        $date = $date->format('d/m/Y H:i');
                        $photo = $value->getPhoto();
                        $content = $value->getContent();

                        include('liste_mess.php');
                    }
                }
                else{
                    $id_mess = $result["data"]["mess"]->getId();
                    $author = $result["data"]["mess"]->getMembre()->getId();
                    $user = $_SESSION["user"];
                    $pseudo = $result["data"]["mess"]->getMembre()->getPseudo();
                    $date = $result["data"]["mess"]->getDate();
                    $photo = $result["data"]["mess"]->getPhoto();
                    $date = new \DateTime($date);
                    $date = $date->format('d/m/Y H:i');
                    $content = $result["data"]["mess"]->getContent();
                    include('liste_mess.php'); 
                }
            }
            else{
                $msg =  "<h2> Syoez le premier à rédiger un message sur ce sujet</h2>";
            }
            ?>
    </div>
</div>
