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

    audio_logs = get_audio_logs((start_bound, end_bound))
    print(audio_logs)
