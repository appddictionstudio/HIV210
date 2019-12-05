<?php
// Template Name: Agendas

// Store the agenda template location
$template       = 'templates/agendas/agenda.php';
$template_empty = 'templates/agendas/agenda_empty.php';

// Setup Agenda Categories
$categories = [
  'planning-council'    => 'Planning Council',
  'executive-committee' => 'Executive (EXEC) Committee',
  'cpcc'                => 'Comprehensive Planning and Continuum of Care (CPCC) Committee',
  'needs-assessment'    => 'Needs Assessment (NA) Committee',
  'fmra'                => 'Fiscal Monitoring & Reallocations (FMRA) Committee',
  'mne'                 => 'Membership/Nominations/Elections (MNE) Committee',
  'peoples-caucus'      => 'People\'s Caucus',
  'ad-hoc'              => 'Ad Hoc Committee',
  'psra'                => 'Priority Setting & Resource Allocation (PSRA) Workshop'
];

// Category counter
$cat_num = 1;

// Get all agendas from DB
$query = new WP_Query([
  'post_type'      => 'agenda',
  'posts_per_page' => -1,
  'meta_key'       => 'agenda_date',
  'orderby'        => 'meta_value_num',
  'order'          => 'DESC'
]);

$agendas = $query->posts;
?>


<section id="reports-page" style='margin-top:40px;margin-bottom:40px;'>
  <div class="container">
    <div class="row">
      <div class="col-xs-12 text-xs-center">
        <h1 class="text-underline">Agendas</h1>
      </div>
    </div>
  </div>

  <?php foreach($categories as $key => $category) : ?>
  <?php
    if($cat_num % 2 == 0) {
      $bg_color = '#ffffff';  // If $cat_num is odd, we make the row background white
    } else {
      $bg_color = '#e3e3e3';  // If $cat_num is even, we make the row background a gray color
    }

    $cat_num++;
  ?>
    <div style='background-color:<?php echo $bg_color; ?>;padding-top:10px;padding-bottom:20px;'>
      <div class='row' style='padding-top:15px;background-color:<?php echo $bg_color; ?>;'>
        <div class="col-xs-12 text-xs-center">
          <h3 style='font-size:1.5em;'><?php echo $category; ?></h3>
        </div>
      </div>
      <div class="row" style='background-color:<?php echo $bg_color; ?>;'>
        <?php
        $count = 0; // Keep track of how many agendas are in DB

          foreach($agendas as $agenda) {
            if($agenda->agenda_category == $key) {
              include $template;
              $count++;
            }
          }

          if($count == 0) { // Happens when there are no template files for category
            include $template_empty;
          }
        ?>
      </div>
    </div>
  <?php endforeach; ?>
</section>
