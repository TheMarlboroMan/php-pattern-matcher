<!DOCTYPE html>
<html>
<head><head>
<body>
<h2>Simple links.</h2>
<ul>
	<li><a href="other.php">Link to existing file</a></li>
	<li><a href="entry_a.html">Link to routed path</a></li>
</ul>

<h2>Form to routed link that sends 33 as the first param and lets you choose
the compulsory parameter.</h2>
<form method="post" action="33/entry_b.html">
	<p>
		Compulsory: <input type="text" name="compulsory" value="Something"/>
	</p>
	<p>
		<input type="submit" value="Send" />
	</p>
</form>

<h2>Form to routed link that sends 22 as the first param and lets you choose
the compulsory parameter AND the optional one.</h2>
<form method="post" action="22/entry_b.html">
	<p>
		Compulsory: <input type="text" name="compulsory" value="Something" />
	</p>
	<p>
		Optional: <input type="text" name="optional" value="Nothing" />
	</p>
	<p>
		<input type="submit" value="Send" />
	</p>
</form>

</body>
</html>
