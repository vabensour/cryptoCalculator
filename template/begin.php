<h2>Add a file</h2>

<?php
if (isset($_GET['upload'])) {
    if ($_GET['upload'] == 'true') {
        echo '<div class="alert alert-success" role="alert">
        File downloaded with success !
      </div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">
        ' . $_GET['uploadError'] . '
      </div>';
    }
}
?>

<form action="scripts/upload.php" method="post" enctype="multipart/form-data">
    <div class="row">
        <div class="col-6">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="fileToUpload" name="fileToUpload">
                <label class="custom-file-label" for="fileToUpload">Choose file</label>
            </div>
        </div>
        <div class="col-3">
            <input type="submit" value="Upload" name="submit" class="btn btn-primary">
        </div>
    </div>
</form>

<h2>Let's go !</h2>

<div class="row">
    <div class="col-12">
        <h3>Did you start this year ?</h3>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="beginYear" checked>
            <label class="form-check-label" for="beginYear">I have started this year, so i don't have to take in account previous years.</label>
        </div>
    </div>

    <div class="col-12 d-none container-previous-year">
        <h3>Previous years</h3>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="previousFileName">File of previous years</label>
                    <select id="previousFileName" class="custom-select">
                        <?php
                        $scandir = scandir("./uploads");
                        foreach ($scandir as $fichier) {
                            if ($fichier != '.' && $fichier != '..') {
                                echo '<option value="' . $fichier . '">' . $fichier . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="previousCashIn">Last cash In</label> 
                    <input class="form-control" id="previousCashIn" aria-describedby="previousCashIn" value="0">
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <h3>Year to calculate</h3>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label for="fileName">File to calculate</label>
            <select id="fileName" class="custom-select">
                <?php
                $scandir = scandir("./uploads");
                foreach ($scandir as $fichier) {
                    if ($fichier != '.' && $fichier != '..') {
                        echo '<option value="' . $fichier . '">' . $fichier . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <label for="typeBroker">Broker</label>
            <select id="typeBroker" class="custom-select">
                <option value="kraken">Kraken</option>
            </select>
        </div>
    </div>
    <div class="col-12 container-btn">
        <button type="button" class="btn btn-primary btn-lg goStep" data-step="1">Verify</button>
        <button type="button" class="btn btn-success btn-lg goStep" data-step="2">Calculate</button>
    </div>
</div>