<script>
	let startTime = $state('')
	let startDate = $state('')
	let endTime = $state('')
	let endDate = $state('')
	let consoleState = $state(false)
	async function handleSubmit(event) {
		event.preventDefault();
		console.log("runs the function")
		consoleState = true;
		// echo "<p>Retrieving audio from " . $_POST["start-date"] . " " . $_POST["start-time"] . " to " . $_POST["end-date"] . " " . $_POST["end-time"] . "...</p>";
		let initial_message = document.createElement('p')
		initial_message.innerText = 'Retrieving audio from ' + startDate + ' ' + startTime + " to " + endDate + ' ' + endTime +  '...'
		document.getElementById('log-retrieval-output').appendChild(initial_message)

		try { 
			const stream = await fetch("/log", {
				method: "POST",
				body: JSON.stringify({
					'start-date': startDate,
					'start-time': startTime,
					'end-date': endDate,
					'end-time': endTime
				})
				//body: new FormData(event.target),
			})/*.then(async (res) => {
				console.log("Getting Reader...")
				let data = res.body.getReader()
				consoleState = true
				console.log("Reading Output...")
				let output = await data.read()
				console.log("Output: ", output)
				let line = document.createElement('p')
				line.innerText = line.innerText + output
				document.getElementById('log-retrieval-output').appendChild(line)
				//document.getElementById('log-retrieval-output').innerHTML = document.getElementById('console_window').innerHTML + output
				console.log("Added to HTML")
			})*/
			const decoder = new TextDecoder();
			let data = stream.body.getReader()
			let done = false
			console.log("Reading output...")
			while (!done) {
				const output = await data.read()
				console.log("Output: ", output)
				if (output.done) { done = true}
				const text = decoder.decode(output.value)
				let line = document.createElement('p')
				line.innerText = text
				document.getElementById('log-retrieval-output').appendChild(line)
			}
			console.log(stream)
			console.log('fetch finished')
			const filename = startDate + '-' + startTime.slice(0,2) + '-' + startTime.slice(3,5) + "-00-" + endDate + '-' + endTime.slice(0,2) + '-' + endTime.slice(3,5) + "-00.mp3"
			const download_link = document.createElement("a");
			download_link.href = "/download/" + filename;
			download_link.download = filename;
			document.body.appendChild(download_link);
			download_link.click();
			document.body.removeChild(download_link);

		} catch (DOMException) {
			console.log("Request never finished")
			let error_message = document.createElement('p')
			error_message.innerText = "Error fetching console output, please standby for file download. \nIf the file download does not start within a reasonable amount of time please try again."

			document.getElementById('log-retrieval-output').appendChild(error_message)
		}
		

		console.log('finished the function')
	}

	// Create query string to pass parameters to the helper
	/*const parameters = new URLSearchParams({
		start_date: "' . $_POST['start-date'] . '",
		start_time: "' . $_POST['start-time'] . '",
		end_date:   "' . $_POST['end-date'] . '",
		end_time:   "' . $_POST['end-time'] . '"
	});

	// Create an event source to get the script log from the server using server-sent event data
	const eventSource = new EventSource("/log/retrieve_log_helper.php?" + parameters);

	// Handle messages from the server using this event source
	eventSource.onmessage = (event) => {
		if (event.data == "[EOF] 0") {
			eventSource.close();
			// Redirect to download
			//   - Creates a temp hyperlink with the download attribute
			const download_link = document.createElement("a");
			download_link.href = "/log/retrieved/'. $filename .'";
			download_link.download = "' . $filename . '";
			document.body.appendChild(download_link);
			download_link.click();
			document.body.removeChild(download_link);
			return;
		}
		else if (event.data.includes("[EOF]")) {
			// Exit on error
			const log_output = document.getElementById("log-retrieval-output");
			log_output.innerHTML += "<p>Exited with error code: " + event.data + "</p>";
			log_output.scrollTop = log_output.scrollHeight;
			eventSource.close();
			return;
		}
		const log_output = document.getElementById("log-retrieval-output");
		if (log_output != null) {            
			log_output.innerHTML += "<p>" + event.data + "</p>";
			log_output.scrollTop = log_output.scrollHeight;
		}
	};*/
</script>


<!-- Includes styling from the page-specific stylesheet -->
<link rel="stylesheet" type="text/css" href="../resources/styles/audio-log.css">

<div class="body-text">
	<!-- Page heading -->
	<h1 class="main-title">
		Audio Log Retriever
	</h1>

	<p class="main-subtitle">
		Use this page to download a specific timeframe of audio from our vast audio log.<br>
	</p>
	<p class="secondary-subtitle">
		DO NOTE: There is a 6 hour maximum limit in terms of the length of the log you wish to request.<br>
		If you wish to request anything longer than this, <a href="/contact">contact</a> a member of the WRPI ecomm!
	</p>
</div>
<!-- Main form -->
<br>
<form id="datetime-form" onsubmit={handleSubmit}>
	<div id="datetime-form-container">
		<div id="datetime-form-start">
			<h2>Starting Timeframe</h2>
			<input required name="start-date" type="date" bind:value={startDate}>
			<input required name="start-time" type="time" bind:value={startTime}>
		</div>

		<div id="datetime-form-end">
			<h2>Ending Timeframe</h2>
			<input required name="end-date" type="date" bind:value={endDate}>
			<input required name="end-time" type="time" bind:value={endTime}>
		</div>
	</div>

	<br><br>
	<div id="datetime-form-submit">
		<!--<?php
		if(isset($_POST['start-date'])) {
			//
			// Process Form Input Here
			//
			echo "<p>Retrieving audio from " . $_POST["start-date"] . " " . $_POST["start-time"] . " to " . $_POST["end-date"] . " " . $_POST["end-time"] . "...</p>";
			echo "<div id='log-retrieval-output'></div>";
			
		}
		?>-->
		<div id='log-retrieval-output' class={consoleState ? "block" : "hidden"}></div>
		<button type="submit">Submit</button>
	</div>
</form>

