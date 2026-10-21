import fs from 'node:fs';



// Sveltekit doesn't like having files hosted on http so this is a workaround

export async function GET({url})  {

    // Isolates filename from incoming url
    const filename = url.href.split('/')[url.href.split('/').length-1]


    // Path assumes running from project home directory
    const file = fs.readFileSync('src/routes/download/retrieved/' + filename)
    
    return new Response(file,
        {
           headers: {
               'Content-Type': 'application/octet-stream',
               'Content-Length': file.length.toString(),
               'Content-Disposition': 'attachment; filename=' + filename
           }
        }
    )
}
