import { spawn } from "child_process";



export async function POST({request}) {
	const formData = await request.json()
	console.log("Got here1")
	// const formData = Object.fromEntries(await request.formData());
	console.log("Got here2")
	console.log(formData)

	const fetchScript = spawn('python3', [
		'./src/lib/retrieve_log.py',
		formData['start-date'],
		formData['start-time'],
		formData['end-date'],
		formData['end-time']
	]);
	console.log("Got here3")
	// console.log(fetchScript)
	let diff = "";

	fetchScript.stdout.on('data', (d) => {
		console.log(d);
		// result = result.concat(d);
		diff = diff.concat(d);
	})
	console.log("Got here4")
	fetchScript.stderr.on('data', (d) => {
		console.log(d);
		diff = diff.concat(d);
	})
	fetchScript.on('close', (code) => {
		
	})
	await fetchScript
	console.log("Got here5")
	let interval: object;
	const stream = new ReadableStream({
		start(controller) {
			fetchScript.stdout.on('data', (d) => {
				console.log(d)
				controller.enqueue(d)
			})
			fetchScript.stderr.on('data', (d) => {
				console.log(d)
				controller.enqueue(d)
			})
			// interval = setInterval(() => {
			// 	controller.enqueue(diff);
			// 	diff = "";
			// }, 200)
			fetchScript.on('close', (code) => {
				controller.close()
			})
		}, pull(controller) {
		},
		cancel() {
			// clearInterval(interval);
		}
	});
	console.log("Response created")
	let res = new Response(stream, {
		headers: {
			'Content-Type': 'text/event-stream',
		}
	})
	console.log(res)
	console.log("Returning")
	return res
}

