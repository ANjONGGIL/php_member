<html>
<body>
<?php
include('dbcon.php');
include('check.php');

if (is_login()){

    if ($_SESSION['user_id'] == 'admin' && $_SESSION['is_admin']==1)
        header("Location: admin.php");
    else
        header("Location: welcome.php");
}



if( ($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit']))
{

    foreach ($_POST as $key => $val)
    {
        if(preg_match('#^an#', $key) === 1){
            $n = substr($key, 2);
            if(isset($_POST[$n])) {
                $_POST[$val] = $_POST[$n];
            }
        }
    }

    $username=$_POST['newusername'];
    $password=$_POST['newpassword'];
    $confirmpassword=$_POST['newconfirmpassword'];
    $userprofile=$_POST['newuserprofile'];


    if ($_POST['newpassword'] != $_POST['newconfirmpassword']) {
        $errMSG = "패스워드가 일치하지 않습니다.";
    }

    if(empty($username)){
        $errMSG = "아이디를 입력하세요.";
    }
    else if(empty($password)){
        $errMSG = "패스워드를 입력하세요.";
    }
    else if(empty($userprofile)){
        $errMSG = "프로필을 입력하세요.";
    }

    try {
        $stmt = $con->prepare('select * from users where username=:username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

    } catch(PDOException $e) {
        die("Database error: " . $e->getMessage());
    }

    $row = $stmt->fetch();
    if ($row){
        $errMSG = "이미 존재하는 아이디입니다.";
    }



    if(!isset($errMSG))
    {
        try{
            $stmt = $con->prepare('INSERT INTO users(username, password, userprofile) VALUES(:username, :password, :userprofile)');
            $stmt->bindParam(':username',$username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':userprofile',$userprofile);

            if($stmt->execute())
            {
                $successMSG = "새로운 사용자를 추가했습니다.";
                header("refresh:1;index.php");
            }
            else
            {
                $errMSG = "사용자 추가 에러";
            }
        } catch(PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }

}

include('head.php');
?>


<div class="container">
    <div>
        <h1 class="h2" align="center">&nbsp; 새로운 사용자 추가</h1><hr>
    </div>
    <?php
    if(isset($errMSG)){
        ?>
        <div class="alert alert-danger">
            <span class="glyphicon glyphicon-info-sign"></span> <strong><?php echo $errMSG; ?></strong>
        </div>
        <?php
    }
    else if(isset($successMSG)){
        ?>
        <div class="alert alert-success">
            <strong><span class="glyphicon glyphicon-info-sign"></span> <?php echo $successMSG; ?></strong>
        </div>
        <?php
    }
    ?>

    <form id="form" method="post" enctype="multipart/form-data" class="form-horizontal" style="margin: 0 300px 0 300px;border: solid 1px;border-radius:4px">
        <table class="table table-responsive">
            <tr>
                <? $r1 = rmd5(rand().mocrotime(TRUE)); ?>
                <td><label class="control-label">아이디</label></td>
                <td><input class="form-control" type="text" name="<? echo $r1; ?>" placeholder="아이디를 입력하세요." autocomplete="off" readonly
                           onfocus="this.removeAttribute('readonly');" />
                    <input type="hidden" name="an<? echo $r1; ?>" value="newusername" />

                </td>
            </tr>
            <tr>
                <? $r2 = rmd5(rand().mocrotime(TRUE)); ?>
                <td><label class="control-label">패스워드</label></td>
                <td>
                    <input class="form-control" type="password" name="<? echo $r2; ?>"  placeholder="패스워드를 입력하세요" autocomplete="off" readonly
                           onfocus="this.removeAttribute('readonly');" />
                    <input type="hidden" name="an<? echo $r2; ?>" value="newpassword" />
                </td>
            </tr>
            <tr>
                <? $r3 = rmd5(rand().mocrotime(TRUE)); ?>
                <td><label class="control-label">패스워드 확인</label></td>
                <td>
                    <input class="form-control" type="password" name="<? echo $r3; ?>"  placeholder="패스워드를 다시 한번 입력하세요" autocomplete="off" readonly
                           onfocus="this.removeAttribute('readonly');" />
                    <input type="hidden" name="an<? echo $r3; ?>" value="newconfirmpassword" />
                </td>
            </tr>

            <tr>
                <? $r4 = rmd5(rand().mocrotime(TRUE)); ?>
                <td><label class="control-label">프로필</label></td>
                <td><input class="form-control" type="text" name="<? echo $r4; ?>" placeholder="프로필을 입력하세요" autocomplete="off" readonly
                           onfocus="this.removeAttribute('readonly');" />
                    <input type="hidden" name="an<? echo $r4; ?>" value="newuserprofile" />
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button type="submit" name="submit"  class="btn btn-primary"><span class="glyphicon glyphicon-floppy-save"></span>&nbsp; 저장</button>
                </td>
            </tr>
        </table>
    </form>
</div>
</body>
</html>