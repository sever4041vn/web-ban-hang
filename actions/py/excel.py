import pandas as pd
import sys
import os   

def convert_to_csv(input_file):
    try:
        output_file = os.path.splitext(input_file)[0] + '.csv'
        # Load the excel file
        df = pd.read_excel(input_file)
        # Export to CSV (index=False prevents adding an extra column for row numbers)
        df.to_csv(output_file, index=False)
        print(output_file)
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    # Pass input and output names from PHP
    convert_to_csv(sys.argv[1])