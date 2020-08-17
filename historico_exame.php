<?php
session_start();
//Data, Médico, Paciente, Receita, Observações, ...
//é visto apenas pelo laboratório e o paciente

require_once '_utilities.php';
require_once '_menu.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['erro'] = maketoast('Usuário não logado', 'Necessário realizar login para utilizar os recursos!');
    header("Location: index.php");
}
if ($_SESSION['tipo'] == 'medico') {
    $_SESSION['erro'] = makeerrortoast('Usuário não permitido', 'O recurso não está disponível para esse usuário');
    header("Location: home.php");
}
if (isset($_SESSION['registro'])) {
    $_registro = unserialize($_SESSION['registro']);
    unset($_SESSION['registro']);
}else
    header("Location: _visualizacao.php?opcao=exame");

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="sortcut icon" href="favicon.ico" type="image/x-icon" />
    <title>iCARE - Visualização de Exames</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="cadastro.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="http://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>
    <script src="historico.js"></script>
    <?php
    if (isset($_SESSION['erro'])) {
        echo $_SESSION['erro'];
        unset($_SESSION['erro']);
    }
    ?>
</head>

<body>
    <div class="container">
        <nav class="navbar navbar-dark bg-dark">
            <a class="navbar-brand" href="home.php">iCARE</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <?php
                    if ($_SESSION['tipo'] == 'admin') {
                        echo makemenuadmin();
                    } else if ($_SESSION['tipo'] == 'paciente') {
                        echo makemenupaciente();
                    } else if ($_SESSION['tipo'] == 'laboratorio') {
                        echo makemenulaboratorio();
                    }
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="_logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
    <br>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Visualizar Exames</li>
                    </ol>
                </nav>
                <h5 class="card-title text-center">Visualizar Exames</h5>
                <form action="_consulta.php" id="cadastroform" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-11">
                            <input type="text" class="form-control" id="pacienteauto" placeholder="Nome do Paciente" aria-label="Nome do Paciente" aria-describedby="basic-addon2">
                            <input type="hidden" id="pacienteid" name="pacienteid" value="">
                            <input type="hidden" id="tipo" name="tipo" value="exame">
                        </div>
                        <div class="form-group col-md-1">
                            <button class="btn btn-outline-primary" type="button">Buscar</button>
                        </div>
                    </div>
                </form>
                <?php
                for ($i = 0; $i < count($_registro); $i++) {
                    echo '<div id="accordion' . $i . '">
                    <div class="card">
                        <div class="card-header" id="heading' . $i . '">
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <h5>' . date("d/m/Y", strtotime($_registro[$i]->consulta_exame->data)) .' - '.$_registro[$i]->paciente->nome. ' - ' . $_registro[$i]->medico->nome . ' - ' . $_registro[$i]->laboratorio->nome .'</h5>
                                </div>
                                <div class="form-group col-md-4">
                                    <div class="text-right">
                                        <button class="btn btn-outline-info" data-toggle="collapse" data-target="#collapse' . $i . '" aria-expanded="false" aria-controls="collapse' . $i . '">
                                            <i class="fas fa-arrow-down" aria-hidden="true"></i>
                                        </button>' .
                        obter_edit_button($_SESSION['tipo'], $_registro[$i]->consulta_exame->id)
                        . '</div>
                                </div>
                            </div>
                            <div id="collapse' . $i . '" class="collapse" aria-labelledby="heading' . $i . '" data-parent="#accordion' . $i . '">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-2">Data:</div>
                                        <div class="col-4">' . $_registro[$i]->consulta_exame->data . '</div>
                                        <div class="col-2">Hora:</div>
                                        <div class="col-4">' . $_registro[$i]->consulta_exame->hora . '</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">Exames:</div>
                                        <div class="col-4">' . $_registro[$i]->consulta_exame->tipoexame . '</div>
                                        <div class="col-2">Outro Exame:</div>
                                        <div class="col-4">' . $_registro[$i]->consulta_exame->outro . '</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-2">Resultado:</div>
                                        <div class="col-4">' . $_registro[$i]->consulta_exame->resultado . '</div>
                                    </div>
                                    ' . obter_observacao($_SESSION['tipo'], $_registro[$i]->consulta_exame->observacao) . '
                                    <br/>
                                    <div id="accordion1_' . $i . '">
                                        <div class="card">
                                            <div class="card-header" id="heading1_' . $i . '">
                                                <div class="form-row">
                                                    <div class="form-group col-md-8">
                                                        <h6>Dados do médico</h6>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="text-right">
                                                            <button class="btn btn-outline-info" data-toggle="collapse" data-target="#collapse1_' . $i . '" aria-expanded="false" aria-controls="collapse1_' . $i . '">
                                                                <i class="fas fa-arrow-down" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="collapse1_' . $i . '" class="collapse" aria-labelledby="heading1_' . $i . '" data-parent="#accordion1_' . $i . '">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-2">Nome:</div>
                                                        <div class="col-4">' . $_registro[$i]->medico->nome . '</div>
                                                        <div class="col-2">CRM:</div>
                                                        <div class="col-4">' . $_registro[$i]->medico->crm . '</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-2">Telefone:</div>
                                                        <div class="col-4">' . $_registro[$i]->medico->telefone . '</div>
                                                        <div class="col-2">E-mail:</div>
                                                        <div class="col-4">' . $_registro[$i]->medico->email . '</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-2">Especialidade:</div>
                                                        <div class="col-10">' . $_registro[$i]->medico->especialidade . '</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="accordion2_' . $i . '">
                                        <div class="card">
                                            <div class="card-header" id="heading2_' . $i . '">
                                                <div class="form-row">
                                                    <div class="form-group col-md-8">
                                                        <h6>Dados do paciente</h6>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="text-right">
                                                            <button class="btn btn-outline-info" data-toggle="collapse" data-target="#collapse2_' . $i . '" aria-expanded="false" aria-controls="collapse2_' . $i . '">
                                                                <i class="fas fa-arrow-down" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="collapse2_' . $i . '" class="collapse" aria-labelledby="heading2_' . $i . '" data-parent="#accordion2_' . $i . '">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-2">Nome:</div>
                                                        <div class="col-4">' . $_registro[$i]->paciente->nome . '</div>
                                                        <div class="col-2">Data Nascimento:</div>
                                                        <div class="col-4">' . $_registro[$i]->paciente->datanascimento . '</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-2">Telefone:</div>
                                                        <div class="col-4">' . $_registro[$i]->paciente->telefone . '</div>
                                                        <div class="col-2">Email:</div>
                                                        <div class="col-4">' . $_registro[$i]->paciente->email . '</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="accordion3_' . $i . '">
                                        <div class="card">
                                            <div class="card-header" id="heading3_' . $i . '">
                                                <div class="form-row">
                                                    <div class="form-group col-md-8">
                                                        <h6>Dados sobre o laboratório</h6>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <div class="text-right">
                                                            <button class="btn btn-outline-info" data-toggle="collapse" data-target="#collapse3_' . $i . '" aria-expanded="false" aria-controls="collapse3_' . $i . '">
                                                                <i class="fas fa-arrow-down" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="collapse3_' . $i . '" class="collapse" aria-labelledby="heading3_' . $i . '" data-parent="#accordion3_' . $i . '">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-2">Nome:</div>
                                                        <div class="col-4">' . $_registro[$i]->laboratorio->nome . '</div>
                                                        <div class="col-2">CNPJ:</div>
                                                        <div class="col-4">' . $_registro[$i]->laboratorio->cnpj . '</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-2">Telefone:</div>
                                                        <div class="col-4">' . $_registro[$i]->laboratorio->telefone . '</div>
                                                        <div class="col-2">E-mail:</div>
                                                        <div class="col-4">' . $_registro[$i]->laboratorio->email . '</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-2">Exames:</div>
                                                        <div class="col-10">' . $_registro[$i]->laboratorio->tipoexame . '</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>