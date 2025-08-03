#include <iostream>
#include <string>
#include <map>
#include "Header_Files/TBS.h"

using namespace std;

map<string, string> user;


int main()
{
    string option;
    string username;
    string password;
    string name;

    Flight demoFlight("04/08/2025", "14:00", "XYZ Airport", "ABC Airport", "F0001AB");

    cout<<"Welcome to XYZ Flight Ticket Booking System!\nChoose an option:\n1) LOGIN\n2) REGISTER\n";
    cin>>option;
    if (option == "1" || convertToLower(option) == "login")
    {
        cout<<"Enter your username: ";
        cin>>username;
        cout<<"Enter your password: ";
        cin>>password;   
        if (user.count(username) > 0)
        {
            if (user[username] != password)
            {
                cout<<"INVALID PASSWORD! ENTER THE PASSWORD AGAIN\n";
            }
        }
        else 
        {
            cout<<"Account does not exist, please try again!\n";
            return 2;
        }
    }
    else if (option == "2" || convertToLower(option) == "register")
    {
        cout<<"Enter a username(no whitespaces): ";
        cin>>username;
        cout<<"Enter an alphanumeric password(atleast one symbol, letter and number): ";
        cin>>password;
        if (user.count(username) == 0)
        {
            user[username] == password;
        }
        else 
        {
            cout<<"Account already exists, please login to your account\n";
            return 3;
        }
    }
    else
    {
        cout<<"INVALID CHOICE!!\n";
        return 1;
    }

    while (true)
    {
        cout<<"\nWelcome "<<username<<" to XYZ Ticket Booking System!\nChoose your service: \n1) Book Ticket\n2) View Bookings\n3) Exit\n";
        cin>>option;
        if (option == "1" || convertToLower(option) == "book ticket")
        {
            cout<<"Enter your first name: ";
            cin>>name;
            if (demoFlight.createBooking(name) == false)
            {
                cout<<"Sorry the flight is full.\n";
            }
        }
        else if (option == "2" || convertToLower(option) == "view bookings")
        {
            demoFlight.viewBookings();
            cout<<"\n";
        }

        else if (option == "3" || convertToLower(option) == "exit")
        {
            cout<<"Thank you for booking your flight tickets through XYZ Booking System!\n";
            break;
        }

        else
        {
            cout<<"INVALID CHOICE\n";
        }
    }
}