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
