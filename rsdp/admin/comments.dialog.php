
<div id="dialogComment" title="Comment Form">

<form name="comments">

<input type="hidden" name="com_id" value="0">
<input type="hidden" name="com_rsdp" value="<?php print $formVars['rsdp']; ?>">
<table class="ui-styled-table">
<tr>
  <th class="ui-state-default">Comment Form</th>
</tr>
<tr>
  <td align="left" class="ui-widget-content">
    <textarea name="com_text" cols="99" rows="5" onKeyDown="textCounter(document.comments.com_text, document.comments.remLen, 1000);" onKeyUp="textCounter(document.comments.com_text, document.comments.remLen, 1000);"></textarea><br>
    <input readonly type="text" name="remLen" size="5" maxlength="5" value="1000"> characters left
  </td>
</tr>
</table>

</form>

</div>

