import { form } from "$app/server";
import { fail } from "@sveltejs/kit";
import { spawnSync } from "child_process";


export const actions = {
	fetchAudio: async ({request}) => {
		const formData = Object.fromEntries(await request.formData());

		const pythonProcess = await spawnSync('python3', [
			'./src/lib/retrieve_log.py',
			formData['start-date'],
			formData['start-time'],
			formData['end-date'],
			formData['end-time']
		]);

		const result = pythonProcess.stdout?.toString()?.trim();
		const error = pythonProcess.stderr?.toString()?.trim();
		console.log(result);
		console.log(error);
	}

}
