<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Comment Management</th>
  <th class="ui-state-default" width="20"><a href="javascript:;" onmousedown="toggleDiv('comment-help');">Help</a></th>
</tr>
</table>

<div id="comment-help" style="display: none">

<div class="main-help ui-widget-content">

<p>Use this section to add comments to be used by the teams performing the tasks. Add notes about special requirements or out of the ordinary changes to standard work.</p>

<ul>
  <li><strong>Buttons</strong>
  <ul>
    <li><strong>Add Comment</strong> - Add a new Comment to the Task.</li>
  </ul></li>
</ul>

</div>

</div>

<table class="ui-styled-table">
<tr>
  <td class="ui-widget-content button">
    <input type="button" name="commentrefresh" value="Refesh Comments" onClick="javascript:attach_comment('<?php print $RSDProot; ?>/admin/comments.mysql.php', -1);">
    <input type="button" name="commentbutton" id="clickAddComment" value="Add Comment"></td>
</tr>
</table>

<span id="comment_mysql"><?php print wait_Process("Loading Comments, Please Wait"); ?></span>

