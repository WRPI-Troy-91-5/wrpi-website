<script>

	// States to store date and time info from form in real time
	let startTime = $state('')
	let startDate = $state('')
	let endTime = $state('')
	let endDate = $state('')
	let consoleState = $state(false)

	// Interrupts form submission to start and receive download
	async function handleSubmit(event) {
		// Prevents normal form submission
		event.preventDefault();
		// Enables the browser console window to show python script output
		consoleState = true;

		// Assigns the filename based on immediate data in case user changes form during download
		const filename = startDate + '-' + startTime.slice(0,2) + '-' + startTime.slice(3,5) + "-00-" + endDate + '-' + endTime.slice(0,2) + '-' + endTime.slice(3,5) + "-00.mp3"

		// Adds an initial message to the console
		let initial_message = document.createElement('p')
		initial_message.innerText = 'Retrieving audio from ' + startDate + ' ' + startTime + " to " + endDate + ' ' + endTime +  '...'
		document.getElementById('log-retrieval-output').appendChild(initial_message)

		try { 
			// Post request to run the python script with parameters and receive console output stream
			const stream = await fetch("/log", {
				method: "POST",
				body: JSON.stringify({
					'start-date': startDate,
					'start-time': startTime,
					'end-date': endDate,
					'end-time': endTime
				})
			})
			// Decoder translates unicode data to console output
			const decoder = new TextDecoder();
			let data = stream.body.getReader()
			let done = false
			// Adds incoming data to the console window until stream finish flag is sent
			while (!done) {
				const output = await data.read()
				if (output.done) { done = true}
				const text = decoder.decode(output.value)
				let line = document.createElement('p')
				line.innerText = text
				let log_output = document.getElementById('log-retrieval-output')
				log_output.appendChild(line)
				log_output.scrollTop = log_output.scrollHeight

			}

			// Download link is created using filename from earlier and /download/ route
			const download_link = document.createElement("a");
			download_link.href = "/download/" + filename;
			download_link.download = filename;
			document.body.appendChild(download_link);
			download_link.click();
			document.body.removeChild(download_link);

		} catch (DOMException) {
			// If the request fails an error message is printed to the console window on the page
			console.log("Request never finished")
			let error_message = document.createElement('p')
			error_message.innerText = "Error fetching console output, please standby for file download. \nIf the file download does not start within a reasonable amount of time please try again."

			document.getElementById('log-retrieval-output').appendChild(error_message)
		}
		

	}

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
		<div id='log-retrieval-output' class={consoleState ? "block" : "hidden"}></div>
		<button type="submit">Submit</button>
	</div>
</form>

