import sys

# Error handler
def error(msg):
    print(f"Error: {msg}")
    print_help()
    exit(1)

def print_help():
    print(f"Usage: python3 {sys.argv[0]} [start_date] [start_time] [end_date] [end_time]")

if __name__ == "__main__":
    if len(sys.argv) != 5:
        error("Inorrect number of arguments passed")
    print(f"Start date: {sys.argv[1]}")
    print(f"Start time: {sys.argv[2]}")
    print(f"End date: {sys.argv[3]}")
    print(f"End time: {sys.argv[4]}")
