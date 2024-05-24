
package homework1;


public class Manager extends Employee {
    
    /// ATTRIBUTES & FIELDS ///
    
    private int teamSize;
    
    /// CONSTRUCTORS ///
    
    public Manager(){
        super();
        teamSize = 0;
    }
    
    public Manager(String n, int i){
        super(n,i);
        teamSize = 0;
    }
    
    public Manager(String n, int i, String d, int teamSize){
        super(n, i, d);  /// για αυτό έφτιαξα τον 4ο Employee constructor
        this.teamSize = teamSize;
    }
    
    /// GETTERS & SETTERS ///
    
    public int getTeamSize(){
        return teamSize;
    }
    
    public void setTeamSize(int teamSize){
        this.teamSize = teamSize;
    }
    
    /// OTHER METHODS ///
    
    @Override
    public void work(){
        System.out.println("Manager: " + getName()+ " is managing a team of "+teamSize+" in: " +getDepartment());
    }
    
    
}
