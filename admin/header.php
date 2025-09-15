<?php
$currentPage = basename($_SERVER['PHP_SELF']); // Get current page name
$nameParam = $_GET['name'] ?? ''; // Safely get ?name=
?>
<style>
    .laststage{
        border-right:none ;
    }
</style>

<div class="content-wrapper">
    <div class="content-header row centered-container">
        <div class="stage-container">
            <a href="themeUpdate.php?name=<?php echo $curruntTheme; ?>">
                <div class="stage <?php echo $currentPage === 'themeUpdate.php' ? 'active-stage' : ''; ?>">
                    Home
                </div>
            </a>
             <a href="addquestions.php?name=<?php echo $nameParam; ?>">
                <div class="stage <?php echo $currentPage === 'addquestions.php' ? 'active-stage' : ''; ?>">
                    Stage 1
                </div>
            </a>
            <a href="editor.php?name=<?php echo $nameParam; ?>">
                <div class="stage <?php echo $currentPage === 'editor.php' ? 'active-stage' : ''; ?>">
                    Stage 2
                </div>
            </a>
           
            <a href="add_words.php?name=<?php echo $nameParam; ?>">
                <div class="stage <?php echo $currentPage === 'add_words.php' ? 'active-stage' : ''; ?>">
                    Stage 3
                </div>
            </a>
            <a href="jigsaw_theme.php?name=<?php echo $nameParam; ?>">
                <div class="laststage stage <?php echo $currentPage === 'jigsaw_theme.php' ? 'active-stage' : ''; ?>">
                    Stage 4
                </div>
            </a>
        </div>
    </div>
</div>
