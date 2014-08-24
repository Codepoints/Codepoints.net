<?php
$title = $font['name'];
$hDescription = '';
include "header.php";
include "nav.php";
?>
<div class="payload font">
  <h1><?php e($title);?></h1>
  <table>
    <tbody>
     <tr>
       <th>Name</th>
       <td><?php e($font['name'])?></td>
     </tr>
     <tr>
       <th>Author</th>
       <td><?php e($font['author'])?><br>
           <?php e($font['copyright'])?></td>
     </tr>
     <?php if (isset($font['publisher']) && $font['publisher']):?>
       <tr>
         <th>Publisher</th>
         <td><?php e($font['publisher'])?></td>
       </tr>
     <?php endif ?>
     <tr>
       <th>URL</th>
       <td><a href="<?php e($font['url'])?>"><?php e($font['url'])?></a></td>
     </tr>
     <?php if (isset($font['license']) && $font['license']):?>
       <tr>
         <th>License</th>
         <td><?php e($font['license'])?></td>
       </tr>
     <?php endif ?>
     <tr>
       <th>Nº of codepoints</th>
       <td><?php e($font['n'])?></td>
     </tr>
     <tr>
       <th></th>
       <td></td>
     </tr>
    </tbody>
  </table>
</div>
<?php include "footer.php"?>
