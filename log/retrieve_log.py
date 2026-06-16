#######################################################################################
# retrieve_log.py                                                                     #
# -------------                                                                       #
# Script to get the a specified range of logs in a single file. This script was       #
# modified from the original `get-audio.py` script I wrote in order to integrate it   #
# with the wrpi.org website but it otherwise carries out the same functionality.      #
#                                                                                     #
# IMPORTANT NOTE                                                                      #
# ---------------                                                                     #
# THIS SCRIPT IS NOT PORTABLE AND IS HARDCODED TO MATCH THIS FILESYSTEM STRUCTURE.    #
# IF YOU ATTEMPT TO USE THIS ON YOUR OWN MACHINE, YOU MUST CHANGE THE FOLDER LOCATION #
# IN EITHER YOUR SYSTEM OR THE CODE.                                                  #
#                                                                                     #
#######################################################################################

###########
# Imports #
###########
import sys
import os
import tempfile
from datetime import datetime, timedelta


#################
# Error handler #
#################
def error(msg):
    print(f"Error: {msg}")
    print_help()
    exit(1)


##########################
# Print CLI Help Message #
##########################
def print_help():
    print(f"Usage: python3 {sys.argv[0]} [start_date] [start_time] [end_date] [end_time]")


#########################################################
# String to Date Object Helper Fuction for File Parsing #
#########################################################
def strtodate_file(string):
    date = None
    try:
        date = datetime.strptime(string, "%Y_%m_%d_%H_%M_%S")
    except:
        try:
            date = datetime.strptime(string, "%Y_%m_%d_%H_%M")
        except:
            date = datetime.strptime(string, "%Y_%m_%d_%I_%M_%p")
    return date


#############################################################
# String to Date Object Helper Function for Request Parsing #
#############################################################
def strtodate_request(string):
    return datetime.strptime(string, "%Y-%m-%d %H:%M")


#########################################
# Date Object to String Helper Function #
#########################################
def datetostr(date):
    return datetime.strftime(date, "%Y-%m-%d-%H-%M-%S")


##################################
# Get the List of All Audio Logs #
##################################
def get_audio_logs(bounds):
    # Get the logs
    logs = os.listdir("/local-zfs/audio-log")
    logs_copy = [] # List to contain the filtered logs
    for i in range(len(logs)):
        # Filter for only mp3 files
        if (logs[i][-4:] != ".mp3"):
            continue

        # Get date from filename (excluding the 3char file extension)
        date = strtodate_file(logs[i][:-4])

        # skip logs that are not within the bounds and given a 30 minute beginning (1800 second) window 
        # Filter the lower bound
        if (date < bounds[0] and abs((date-bounds[0]).total_seconds()) >= 1800):                                                                     
            continue

        # Filter the upper bound, any log exceeding this will be out of bounds
        if (date > bounds[1]):
            continue

        # Add the logs that make it past the filter
        logs_copy.append(logs[i])

    # Sort the unfiltered list
    logs_copy.sort()
    return logs_copy


###############
# Entry Point #
###############
if __name__ == "__main__":
    if len(sys.argv) != 5:
        error("Inorrect number of arguments passed")
    
    # Process bounds
    start_bound = strtodate_request(f'{sys.argv[1]}  {sys.argv[2]}')
    end_bound   = strtodate_request(f'{sys.argv[3]}  {sys.argv[4]}')

    # Confirm that the bounds are no longer than 6 hours apart
    length = end_bound - start_bound
    if (length > timedelta(hours=6)):
        print(f"ERROR: No bounds may be entered over 6 hours apart, requested log length: {length}")
        exit(1)

    # Get list of log files that are within the bounds
    audio_logs = get_audio_logs((start_bound, end_bound))
    print(f"List of logs to process: {audio_logs}")

    print("Creating a temporary folder for log processing...", end=" ")
    temp = tempfile.mkdtemp()
    print("Done")

    print("Copying the audio logs to the temporary directory for processing...")
    ret = os.system(f"cd /local-zfs/audio-log && cp -v {' '.join(audio_logs)} {temp}")
    if (ret != 0):
        quit(ret)
    print("Done")

    # If there are two or more clips the bounding clips will have to be cut/adjusted and recombined
    if (len(audio_logs) >= 2):
        # Find how many seconds into the first clip and from the end of the last clip need to be cut out
        begin_cut_time = int(abs((start_bound-strtodate_file(audio_logs[0][:-4])).total_seconds()))
        end_cut_time = int(abs((end_bound-strtodate_file(audio_logs[-1][:-4])).total_seconds()))
        # The above time is just how much needs to be removed
        # Need to calculate the total time of the new clip for processing

        # Need to define a list to store the lost files caused by these operations
        saved_logs = []
        # Cut the beginning segment of the first log if needed
        if (begin_cut_time != 0):
            ret = os.system(f"cd {temp} && ffmpeg -ss {begin_cut_time} -i {audio_logs[0]} new_temp_start.mp3")
            if (ret != 0):
                quit(ret)
            saved_logs.append(audio_logs[0])
            audio_logs[0] = "new_temp_start.mp3"
    
        # Cut the end segment of the last log if needed
        if (end_cut_time != 0):
            ret = os.system(f"cd {temp} && ffmpeg -t {end_cut_time} -i {audio_logs[-1]} new_temp_end.mp3")
            if (ret != 0):
                quit(ret)
            saved_logs.append(audio_logs[-1])
            audio_logs[-1] = "new_temp_end.mp3"

        # Combine the logs into a single file
        ret = os.system(f"cd {temp} && ffmpeg -i 'concat:{'|'.join(audio_logs)}' -acodec copy ./{datetostr(start_bound)}-{datetostr(end_bound)}.mp3")    
        if (ret != 0):
            quit(ret)

        # Move file to a downloadable directory
        ret = os.system(f"mkdir -pv ./retrieved && mv -v {temp}/{datetostr(start_bound)}-{datetostr(end_bound)}.mp3 ./retrieved")
        if (ret != 0):
            quit(ret)
        print(f"File: {datetostr(start_bound)}-{datetostr(end_bound)}.mp3")

        # Remove the temp audio logs
        ret = os.system(f"cd {temp} && rm -v {' '.join(audio_logs)} {' '.join(saved_logs)}")
        if (ret != 0):
            quit(ret)

    # Case of a single log
    elif (len(audio_logs) == 1):
        # Find how many seconds to skip into and cut from the end of the single log
        begin_cut_time = int(abs(strtodate_file(audio_logs[0][:-4])-start_bound).total_seconds())
        end_cut_time = int(abs((end_bound-strtodate_file(audio_logs[0][:-4])).total_seconds()))
        # Total segment = end bound - begin bound
        total_time_segment = int((end_bound - start_bound).total_seconds())

        # Use ffmpeg to seek into and cut from the end
        ret = os.system(f"cd {temp} && ffmpeg -ss {begin_cut_time} -t {total_time_segment} -i {audio_logs[0]} ./{datetostr(start_bound)}-{datetostr(end_bound)}.mp3")
        if (ret != 0):
            quit(ret)

        # Move file to a downloadable directory
        ret = os.system(f"mkdir -pv ./retrieved && mv -v {temp}/{datetostr(start_bound)}-{datetostr(end_bound)}.mp3 ./retrieved")
        if (ret != 0):
            quit(ret)
        print(f"File: {datetostr(start_bound)}-{datetostr(end_bound)}.mp3")

    else:
        print("No logs match your bound criteria") 
