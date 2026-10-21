import { spawn } from "child_process";



export async function POST({request}) {
	const formData = await request.json()


	// Starts the python script asynchronously so we can send console output to the client
	const fetchScript = spawn('python3', [
		'./src/lib/retrieve_log.py',
		formData['start-date'],
		formData['start-time'],
		formData['end-date'],
		formData['end-time']
	]);


	// Constructs a stream to send console output to the client
	const stream = new ReadableStream({
		start(controller) {
			// At the start of the stream it calls these functions
			// The python script now sends output to be queued to be sent to the client automatically
			// The idea for this implementation came to me in a dream
			fetchScript.stdout.on('data', (d) => {
				controller.enqueue(d)
			})
			fetchScript.stderr.on('data', (d) => {
				controller.enqueue(d)
			})
			fetchScript.on('close', (code) => {
				controller.close()
			})
		}
	});
	let res = new Response(stream, {
		headers: {
			'Content-Type': 'text/event-stream',
		}
	})
	return res
}

