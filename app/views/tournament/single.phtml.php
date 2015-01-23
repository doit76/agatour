<h3><?php echo $tournament->post_title; ?></h3>
<p><?php echo $tournament->date; ?></p>

    <style>
        table.tournament {
            border-collapse: collapse;
            width: 100%; }

        table.tournament thead tr td {
            text-transform: uppercase;
            vertical-align: middle;
            border: none;
            font-weight: 700;
            font-size: 12px;
            background:linear-gradient(to bottom,#005391 0,#004987  100%);
              filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#005391',endColorstr='#004987 ',GradientType=0);
            color: #fff;
            padding: 5px 10px; }

        table.tournament tr td {
            text-align: center;
            vertical-align: middle;
            border: 1px #d8d8d8 solid;
            font-size: 14px; 
            padding: 10px 5px; }

        table.tournament tr td.name {
            text-align: left; } 

        table.tournament tr td img {
            width: 25px; } 

        table.tournament tr td.team { 
            width: 40% }

        table.tournament tr td.score { 
            width: 20% } 
        
        table.scorecard {
            margin: 0;
            border-collapse: collapse;
            background: #F6F6F6;
            color: #303030; }

        table.scorecard tr td {
            padding: 0;
            font-size: 10px;
            border: 1px solid #e2e2e2; }

        table.scorecard tr td.hole {
            width: 25px; }

        table.scorecard tr td.eagle {
            color: #767676;
            background: #26A3E4; }

        table.scorecard tr td.birdie {
            color: #767676;
           background: #9EC9F5; }

        table.scorecard tr td.par {
            background: #F6F6F6; }

        table.scorecard tr td.bogey {
            color: #767676;
            background: #F5AD28 ; }

        table.scorecard tr td.double-bogey {
            color: #d0d0d0;
           background: #ea4500; }

        table.scorecard tr td.threeplus {
            color: #d0d0d0;
            background: #8e4600; }
                
    </style>

<?php if ($tournament->scoring == 'stroke') { ?>
    <?php echo $this->partial('tournament/stroke'); ?>
<?php } elseif ($tournament->scoring == 'match') { ?>
    <?php echo $this->partial('tournament/match'); ?>
<?php } elseif ($tournament->scoring == 'cup') { ?>
    <?php echo $this->partial('tournament/cup'); ?>
<?php } ?>