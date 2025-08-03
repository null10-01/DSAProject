#include "TBS.h"

#include <iostream>
#include <string>

using namespace std;

string convertToLower(string og)
{
    string newString;
    for (int i = 0; i < og.length(); i++)
    {
        newString += tolower(og[i]);
    }
    return newString;
}

bool Flight::createBooking(string name)
{
    if (numberBooked >= maxSeats)
    {
        cout<<"Invalid booking as seats are full!";
        return false;
    }
    Passenger p(name, flightID+to_string(numberBooked + 1), numberBooked + 1);
    bookings[numberBooked] = p;
    cout<<"\nTicket booking completed!\nTicket Details:\nName: "<<p.name<<" ticketID: "<<p.ticketID<<" Seat number: "<<p.seatNumber<<"\n";
    numberBooked++;
    return true;
}

void Flight::viewBookings()
{
    cout<<"Booked Ticket Details:\n";
    if (numberBooked == 0)
    {
        cout<<"No bookings made yet!";
        return;
    }
    else
    {
        for (int i = 0; i < numberBooked; i++)
        {
            cout<<"Name: "<<bookings[i].name<<" | TicketID: "<<bookings[i].ticketID<<" | Seat Number: "<<bookings[i].seatNumber<<"\n";
        }
    }
}